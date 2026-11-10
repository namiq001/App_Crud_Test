<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view("admin.index");
    }

    // Admin Brands
    public function brands()
    {
        $query = Brand::query();

        if ($search = request('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $brands = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();

        return view('admin.brands', compact('brands'));
    }

    public function brandAdd()
    {
        return view('admin.brand-add');
    }

    public function brandStore(Request $request)
    {
        $request->merge([
            'slug' => Str::slug($request->slug ?: $request->name),
        ]);

        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:brands,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'nullable|boolean'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/brands'), $imageName);

            $this->generateThumbnailImage(
                public_path('uploads/brands/' . $imageName),
                $imageName,
                'uploads/brands'
            );

            $brand->image = $imageName;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('success', 'Brand added successfully');
    }

    public function generateThumbnailImage($imagePath, $imageName, $folder, $width = 124, $height = 124)
    {
        $thumbnailPath = public_path($folder . '/thumbnails');

        if (!file_exists($thumbnailPath)) {
            mkdir($thumbnailPath, 0755, true);
        }

        Image::decode($imagePath)
            ->cover($width, $height)
            ->save($thumbnailPath . '/' . $imageName);
    }

    public function brandEdit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brandUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:brands,slug,' . $id,
            'image' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $brand->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            //Delete old image
            if ($brand->image) {
                @unlink(public_path('uploads/brands/' . $brand->image));
                @unlink(public_path('uploads/brands/thumbnails' . $brand->image));
            };

            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $this->generateThumbnailImage($request->image, $imageName, 'uploads/brands', 124, 124);
            $request->image->move(public_path('uploads/brands'), $imageName);
            $brand->image = $imageName;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('success', 'Brand updated successfully');
    }

    public function brandDelete($id)
    {
        $brand = Brand::findOrFail($id);

        //Delete image
        if ($brand->image) {
            @unlink(public_path('uploads/brands/' . $brand->image));
            @unlink(public_path('uploads/brands/thumbnails/' . $brand->image));
        }

        $brand->delete();

        return back()->with('success', 'Brand delete successfully');
    }

    //Admin Category
    public function categories()
    {
        $query = Category::query();

        if ($search = request('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $categories = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();

        return view('admin.categories', compact('categories'));
    }

    public function categoryAdd()
    {
        $parentCategories = Category::where('parent_id', null)->orderBy('name', 'ASC')->get();
        return view('admin.category-add', compact('parentCategories'));
    }

    public function categoryStore(Request $request)
    {
        $request->merge([
            'slug' => Str::slug($request->slug ?: $request->name),
        ]);

        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'nullable|boolean'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->parent_id = $request->parent_id;
        $category->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);

            $this->generateThumbnailImage(
                public_path('uploads/categories/' . $imageName),
                $imageName,
                'uploads/categories'
            );

            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Category added successfully');
    }

    public function categoryEdit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::where('parent_id', null)->where('id', '!=', $category->id)->orderBy('name', 'ASC')->get();
        return view('admin.category-edit', compact('category', 'parentCategories'));
    }

    public function categoryUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $id,
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $category->status = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            //Delete old image
            if ($category->image) {
                @unlink(public_path('uploads/categories/' . $category->image));
                @unlink(public_path('uploads/categories/thumbnails' . $category->image));
            };

            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $this->generateThumbnailImage($request->image, $imageName, 'uploads/categories/', 124, 124);
            $request->image->move(public_path('uploads/categories/'), $imageName);
            $category->image = $imageName;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully');
    }

    public function categoryDelete($id)
    {
        $category = Category::findOrFail($id);

        //Delete image
        if ($category->image) {
            @unlink(public_path('uploads/categories/' . $category->image));
            @unlink(public_path('uploads/categories/thumbnails/' . $category->image));
        }

        $category->delete();

        return back()->with('success', 'Category delete successfully');
    }
}
