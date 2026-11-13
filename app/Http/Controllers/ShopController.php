<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function shop(Request $request)
    {
        $categories = Category::withCount(['products' => fn($q) => $q->where('status', true)])
            ->orderBy('name')
            ->get();

        $brands = Brand::withCount(['products' => fn($q) => $q->where('status', true)])
            ->orderBy('name')
            ->get();

        $sort = $request->input('sort', 'featured');
        $selected = (array) $request->input('categories', []);
        $selectedBrand = $request->input('brand');
        $search = trim((string) $request->input('search', ''));

        // Slider hədləri
        $priceBounds = Product::where('status', true)
            ->selectRaw('MIN(COALESCE(sale_price, regular_price)) as min_p, MAX(COALESCE(sale_price, regular_price)) as max_p')
            ->first();

        $priceMin = (int) floor($priceBounds->min_p ?? 0);
        $priceMax = (int) ceil($priceBounds->max_p ?? 500);

        $minPrice = $request->filled('min_price') ? (float) $request->min_price : $priceMin;
        $maxPrice = $request->filled('max_price') ? (float) $request->max_price : $priceMax;

        // Sorğunu qururuq (hələ icra olunmur)
        $query = Product::where('status', true)
            ->when($selected, fn($q) => $q->whereIn('category_id', $selected))
            ->when($selectedBrand, fn($q) => $q->where('brand_id', $selectedBrand))
            ->when($search !== '', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->whereRaw('COALESCE(sale_price, regular_price) BETWEEN ? AND ?', [$minPrice, $maxPrice]);

        // Sıralama
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, regular_price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, regular_price) DESC'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc'),
        };

        // Yalnız burada icra olunur
        $products = $query->paginate(10)->withQueryString();

        return view('shop.index', compact(
            'products',
            'categories',
            'selected',
            'search',
            'priceMin',
            'priceMax',
            'minPrice',
            'maxPrice',
            'brands',
            'selectedBrand',
            'sort'
        ));
    }

    public function productDetails(string $slug)
    {
        $product = Product::where("slug", $slug)->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('shop.details', compact('product', 'relatedProducts'));
    }


}
