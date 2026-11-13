<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;

Route::get("/", [HomeController::class, "index"])->name("home.index");
Route::get('/category/{slug}', [HomeController::class, 'categoryShow'])->name('category.show');
Route::get('/shop', [ShopController::class, 'shop'])->name('shop');
Route::get('/shop/{slug}', [ShopController::class, 'productDetails'])->name('shop.details');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('index', [UserController::class, 'index'])->name('users.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware([AuthAdmin::class])->group(function () {
    // middleware
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    //brands
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand-add', [AdminController::class, 'brandAdd'])->name('admin.brand.add');
    Route::post('/admin/brand/store', [AdminController::class, 'brandStore'])->name('admin.brand.store');
    Route::get('/admin/brand-edit/{id}', [AdminController::class, 'brandEdit'])->name('admin.brand.edit');
    Route::put('/admin/brand-edit/{id}', [AdminController::class, 'brandUpdate'])->name('admin.brand.update');
    Route::delete('/admin/brand-delete/{id}', [AdminController::class, 'brandDelete'])->name('admin.brand.delete');

    //Custemers
    Route::get('/admin/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::delete('/admin/customer-delete/{id}', [AdminController::class, 'customerDelete'])->name('admin.customer.delete');

    //categories
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/category-add', [AdminController::class, 'categoryAdd'])->name('admin.category.add');
    Route::post('/admin/category/store', [AdminController::class, 'categoryStore'])->name('admin.category.store');
    Route::get('/admin/category-edit/{id}', [AdminController::class, 'categoryEdit'])->name('admin.category.edit');
    Route::put('/admin/category-update/{id}', [AdminController::class, 'categoryUpdate'])->name('admin.category.update');
    Route::delete('/admin/category-delete/{id}', [AdminController::class, 'categoryDelete'])->name('admin.category.delete');

    //products
    Route::get('/admin/products', [ProductController::class, 'products'])->name('admin.products');
    Route::get('/admin/product-add', [ProductController::class, 'productAdd'])->name('admin.product.add');
    Route::post('/admin/product-store', [ProductController::class, 'productStore'])->name('admin.product.store');
    Route::get('/admin/product-edit/{id}', [ProductController::class, 'productEdit'])->name('admin.product.edit');
    Route::put('/admin/product-update/{id}', [ProductController::class, 'productUpdate'])->name('admin.product.update');
    Route::delete('/admin/product-delete/{id}', [ProductController::class, 'productDelete'])->name('admin.product.delete');
});

require __DIR__ . '/auth.php';
