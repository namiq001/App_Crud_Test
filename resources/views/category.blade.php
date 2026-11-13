<x-app-layout>
    <section class="relative bg-gray-50 overflow-hidden">
        <div class="swiper-container main-slider h-[500px] lg:h-[600px]">
            <div class="swiper-wrapper">
                <div class="swiper-slide flex items-center justify-center">
                    <div class="container mx-auto px-4 grid lg:grid-cols-2 gap-8 items-center h-full">
                        <div class="text-center lg:text-left space-y-4 animate-fadeIn">
                            <h2 class="text-4xl lg:text-6xl font-bold text-gray-800 leading-tight">New Stylish <br />
                                Decor Furniture</h2>
                            <p class="text-lg text-gray-600">Unique Furniture Style Design for Your Family</p>
                            <a href="shop.php"
                                class="inline-block bg-primary text-white px-8 py-3 rounded hover:bg-blue-600 transition shadow-lg mt-4">Purchase
                                Now</a>
                        </div>
                        <div class="hidden lg:block">
                            <img src="{{ asset('assets/images/slider/slider-item-1.png') }}" alt="Furniture" class="mx-auto" />
                        </div>
                    </div>
                </div>
                <div class="swiper-slide flex items-center justify-center">
                    <div class="container mx-auto px-4 grid lg:grid-cols-2 gap-8 items-center h-full">
                        <div class="text-center lg:text-left space-y-4">
                            <h2 class="text-4xl lg:text-6xl font-bold text-gray-800 leading-tight">Modern Living <br />
                                Room Sets</h2>
                            <p class="text-lg text-gray-600">Comfort and Style combined.</p>
                            <a href="shop.php"
                                class="inline-block bg-primary text-white px-8 py-3 rounded hover:bg-blue-600 transition shadow-lg mt-4">Shop
                                Now</a>
                        </div>
                        <div class="hidden lg:block">
                            <img src="{{ asset('assets/images/slider/slider-item-2.png') }}" alt="Furniture" class="mx-auto" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-10 text-center uppercase tracking-widest text-gray-800">
                {{ $category->name }}
            </h2>

            @if ($children->count())
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($children as $child)
                        <div class="text-center group">
                            <a href="{{ route('category.show', $child->slug) }}"
                                class="block bg-white rounded-2xl p-6 transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                                <img src="{{ $child->image ? asset('uploads/categories/thumbnails/' . $child->image) : 'https://placehold.co/96x96?text=No+Image' }}"
                                    class="mx-auto w-24 h-24 object-contain" alt="{{ $child->name }}">
                            </a>
                            <h4 class="mt-5 font-semibold text-gray-600 uppercase text-xs tracking-widest">
                                {{ $child->name }}
                            </h4>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500">No subcategories in this category.</p>
            @endif
        </div>
    </section>

    
</x-app-layout>