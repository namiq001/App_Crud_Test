<?php

namespace App\Http\Controllers;

use File;
use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Str;

class ProductController extends Controller
{
    public function products(Request $request)
    {
        $products = Product::with(['brand', 'category'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('SKU', 'like', $term);
                });
            })
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->category))
            ->when($request->filled('status'), function ($q) use ($request) {
                match ($request->status) {
                    'published' => $q->where('status', 1),
                    'draft' => $q->where('status', 0),
                    'outofstock' => $q->where('stock_status', 'outofstock'),
                    default => null,
                };
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products', compact('products', 'categories'));
    }

    public function productAdd()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));

    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'SKU' => 'required|string|max:100|unique:products,SKU',
            'stock_status' => 'required|in:instock,outofstock',
            'quantity' => 'required|integer|min:0',
            'featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->information = $request->information;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->boolean('featured');
        $product->status = $request->boolean('status');
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->resizeAndSaveImage($image, $imageName, 'uploads/products/', 570, 604);
            $this->resizeAndSaveImage($image, $imageName, 'uploads/products/thumbnails/', 270, 303);
            $product->image = $imageName;
        }

        $galery_arr = array();
        $galery_images = "";
        $counter = 1;

        if ($request->hasFile("images")) {
            $allowedFileExtion = ['jpg', 'png', 'jpeg', 'webp'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                if (in_array(strtolower($gextension), $allowedFileExtion)) {
                    $gimageName = $current_timestamp . '-' . $counter . '.' . $gextension;
                    $this->resizeAndSaveImage($file, $gimageName, 'uploads/products/', 570, 604);
                    $this->resizeAndSaveImage($file, $gimageName, 'uploads/products/thumbnails/', 270, 303);
                    array_push($galery_arr, $gimageName);
                    $counter++;
                }
            }
            $galery_images = implode(',', $galery_arr);
        }
        $product->images = $galery_images ?: null;

        $product->save();


        return redirect()->route('admin.products')->with('success', 'Product add successfully');

    }

    public function resizeAndSaveImage($image, $imageName, $folder, $width = 270, $height = 303)
    {
        $imagePath = public_path($folder);
        if (!file_exists($imagePath)) {
            mkdir($imagePath, 0755, true);
        }
        Image::decode($image)->cover($width, $height)->save($imagePath . '/' . $imageName);
    }

    public function productEdit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function productUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'SKU' => 'required|string|max:100|unique:products,SKU,' . $product->id,
            'stock_status' => 'required|in:instock,outofstock',
            'quantity' => 'required|integer|min:0',
            'featured' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
        ]);


        $product->name = $request->name;
        $product->slug = $request->slug ? Str::slug($request->slug) : Str::slug($product->name);
        $product->short_description = $request->short_description;
        $product->information = $request->information;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->boolean('featured');
        $product->status = $request->boolean('status');
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path('uploads/products' . $product->image))) {
                @unlink(public_path('uploads/products' . $product->image));
            }
            if ($product->image && File::exists(public_path('uploads/products/thumbnails' . $product->image))) {
                @unlink(public_path('uploads/products/thumbnails' . $product->image));
            }

            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->resizeAndSaveImage($image, $imageName, 'uploads/products', 570, 604);
            $this->resizeAndSaveImage($image, $imageName, 'uploads/products/thumbnails', 270, 303);

            $product->image = $imageName;
        }

        $galery_arr = $product->images ? explode(',', $product->images) : [];

        if ($request->has('deleted_galery_images') && is_array($request->deleted_galery_images)) {
            foreach ($request->deleted_galery_images as $deletedImage) {
                if ($deletedImage && File::exists(public_path('uploads/products' . $product->deletedImage))) {
                    @unlink(public_path('uploads/products' . $product->image));
                }
                if ($deletedImage && File::exists(public_path('uploads/products/thumbnails' . $product->deletedImage))) {
                    @unlink(public_path('uploads/products/thumbnails' . $product->image));
                }
                if (($key = array_search($deletedImage, $galery_arr)) !== false) {
                    unset($galery_arr[$key]);
                }
            }
            $galery_arr = array_values($galery_arr);
        }

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $counter = 1;

            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gimageName = $current_timestamp . '-' . $counter . '.' . $gextension;

                $this->resizeAndSaveImage($file, $gimageName, 'uploads/products/', 570, 604);
                $this->resizeAndSaveImage($file, $gimageName, 'uploads/products/thumbnails/', 270, 303);

                array_push($galery_arr, $gimageName);
                $counter++;

            }
        }

        $product->images = !empty($galery_arr) ? implode(',', $galery_arr) : null;

        $product->save();

        return redirect()->route('admin.products')->with('success', 'Product Updated successfully');

    }

    public function productDelete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && File::exists(public_path('uploads/products' . $product->image))) {
            unlink(public_path('uploads/products' . $product->image));
        }
        if ($product->image && File::exists(public_path('uploads/products/thumbnails' . $product->image))) {
            unlink(public_path('uploads/products/thumbnails' . $product->image));
        }

        if ($product->images) {
            $gallery_images = explode(',', $product->images);
            foreach ($gallery_images as $gallery_image) {
                if ($gallery_image && File::exists(public_path('uploads/products' . $gallery_image))) {
                    unlink(public_path('uploads/products' . $gallery_image));
                }
                if ($gallery_image && File::exists(public_path('uploads/products/thumbnails' . $gallery_image))) {
                    unlink(public_path('uploads/products/thumbnails' . $gallery_image));
                }
            }
        }
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }
}