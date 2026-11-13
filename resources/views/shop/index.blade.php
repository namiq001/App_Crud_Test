<x-app-layout>

    <style>
        .price-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            pointer-events: auto;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3b82f6;
            /* öz primary rənginiz */
            border: 2px solid #fff;
            box-shadow: 0 0 2px rgba(0, 0, 0, .4);
            cursor: pointer;
        }

        .price-range::-moz-range-thumb {
            pointer-events: auto;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3b82f6;
            border: 2px solid #fff;
            cursor: pointer;
        }
    </style>

    <div class="relative bg-sky-700 text-white h-64 flex items-center justify-center bg-cover bg-center"
        style="background-image: url('assets/images/page-banner.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-4xl font-bold mb-2">Shop</h2>
            <ul class="flex justify-center space-x-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
                <li>/</li>
                <li class="text-primary">Shop</li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="flex flex-col lg:flex-row gap-8">

            <aside class="w-full lg:w-1/4 order-2 lg:order-1 space-y-8">

                <div class="bg-gray-50 p-6 rounded-lg border">
                    <form id="search-form" method="GET" action="{{ route('shop') }}" class="relative">
                        {{-- Seçilmiş kateqoriyalar axtarışda itməsin --}}
                        @foreach ($selected as $catId)
                            <input type="hidden" name="categories[]" value="{{ $catId }}">
                        @endforeach

                        <input type="text" id="shop-search" name="search" value="{{ $search }}" autocomplete="off"
                            placeholder="Search product..."
                            class="w-full border p-3 rounded focus:outline-none focus:border-primary pr-10">

                        <button type="submit" class="absolute right-3 top-3 text-gray-400 hover:text-primary">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>

                    @if ($search !== '')
                        <a href="{{ route('shop', ['categories' => $selected]) }}"
                            class="text-sm text-gray-500 hover:text-red-500 mt-3 inline-block">
                            <i class="fa-solid fa-xmark mr-1"></i> Clear search
                        </a>
                    @endif
                </div>


                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h4 class="font-bold text-lg mb-4">Categories</h4>

                    <form id="category-filter" method="GET" action="{{ route('shop') }}">
                        <ul class="space-y-3">
                            @forelse ($categories as $category)
                                @php $isChecked = in_array($category->id, $selected); @endphp
                                <li>
                                    <label class="flex items-center cursor-pointer hover:text-primary">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                            class="category-checkbox hidden" {{ $isChecked ? 'checked' : '' }}>

                                        <span
                                            class="w-4 h-4 border rounded mr-3 flex items-center justify-center transition"
                                            style="{{ $isChecked ? 'background:#3b82f6;border-color:#3b82f6;' : 'background:#fff;border-color:#d1d5db;' }}">
                                            @if ($isChecked)
                                                <i class="fa-solid fa-check" style="color:#fff;font-size:9px;"></i>
                                            @endif
                                        </span>

                                        <span
                                            class="{{ $isChecked ? 'font-semibold text-primary' : '' }}">{{ $category->name }}</span>
                                        <span class="text-gray-400 text-sm ml-1">({{ $category->products_count }})</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-sm text-gray-500">No categories</li>
                            @endforelse
                        </ul>
                    </form>

                    @if (count($selected))
                        <a href="{{ route('shop') }}" class="text-sm text-gray-500 hover:text-red-500 mt-4 inline-block">
                            <i class="fa-solid fa-xmark mr-1"></i> Clear filters
                        </a>
                    @endif
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h4 class="font-bold text-lg mb-4">Filter By Price</h4>

                    <form id="price-form" method="GET" action="{{ route('shop') }}">
                        {{-- Digər filtrlər itməsin --}}
                        @foreach ($selected as $catId)
                            <input type="hidden" name="categories[]" value="{{ $catId }}">
                        @endforeach
                        @if ($search !== '')
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif

                        <div class="relative pt-6 pb-2">
                            <div class="relative w-full h-1 bg-gray-300 rounded">
                                <div class="absolute h-1 bg-primary rounded" id="range-track"></div>
                            </div>

                            <input type="range" min="{{ $priceMin }}" max="{{ $priceMax }}" value="{{ $minPrice }}"
                                class="price-range absolute w-full h-1 bg-transparent appearance-none top-6 left-0 pointer-events-none z-20"
                                id="range-min">
                            <input type="range" min="{{ $priceMin }}" max="{{ $priceMax }}" value="{{ $maxPrice }}"
                                class="price-range absolute w-full h-1 bg-transparent appearance-none top-6 left-0 pointer-events-none z-20"
                                id="range-max">

                            <input type="hidden" name="min_price" id="min-price-input" value="{{ $minPrice }}">
                            <input type="hidden" name="max_price" id="max-price-input" value="{{ $maxPrice }}">

                            <div class="flex justify-between mt-4 text-sm font-medium text-gray-600">
                                <span>$<span id="price-min-display">{{ $minPrice }}</span></span>
                                <span>$<span id="price-max-display">{{ $maxPrice }}</span></span>
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-3 w-full bg-primary hover:bg-blue-600 text-white py-2 rounded text-sm font-medium transition">
                            Filter
                        </button>
                    </form>

                    @if (request()->hasAny(['min_price', 'max_price']))
                        <a href="{{ route('shop', array_filter(['categories' => $selected, 'search' => $search])) }}"
                            class="text-sm text-gray-500 hover:text-red-500 mt-3 inline-block">
                            <i class="fa-solid fa-xmark mr-1"></i> Reset price
                        </a>
                    @endif
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h4 class="font-bold text-lg mb-4">Brands</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($brands as $brand)
                            @php
                                $isActive = (string) $selectedBrand === (string) $brand->id;
                                $params = array_filter([
                                    'categories' => $selected,
                                    'search' => $search,
                                    'min_price' => request('min_price'),
                                    'max_price' => request('max_price'),
                                    // aktivə yenidən basanda brand filtri götürülsün
                                    'brand' => $isActive ? null : $brand->id,
                                ]);
                            @endphp
                            <a href="{{ route('shop', $params) }}"
                                class="px-3 py-1 border rounded text-sm transition
                                    {{ $isActive ? 'bg-primary text-white' : 'bg-white hover:bg-primary hover:text-white' }}">
                                {{ $brand->name }} ({{ $brand->products_count }})
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">No brands</p>
                        @endforelse
                    </div>
                </div>

            </aside>

            <div class="w-full lg:w-3/4 order-1 lg:order-2">

                <div
                    class="flex flex-col sm:flex-row justify-between items-center bg-white border p-4 rounded mb-8 shadow-sm">
                    <p class="text-sm mb-4 sm:mb-0">
                        Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of
                        {{ $products->total() }} Results
                    </p>

                    <div class="flex items-center space-x-6">
                        <div class="flex space-x-2">
                            <button id="btn-grid"
                                class="w-8 h-8 flex items-center justify-center rounded bg-primary text-white transition">
                                <i class="fa fa-th"></i>
                            </button>
                            <button id="btn-list"
                                class="w-8 h-8 flex items-center justify-center rounded bg-gray-200 hover:bg-primary hover:text-white transition">
                                <i class="fa fa-list"></i>
                            </button>
                        </div>

                        <div class="flex items-center">
                            <form id="sort-form" method="GET" action="{{ route('shop') }}" class="flex items-center">

                                @foreach ($selected as $catId)
                                    <input type="hidden" name="categories[]" value="{{ $catId }}">
                                @endforeach
                                @if ($selectedBrand)
                                    <input type="hidden" name="brand" value="{{ $selectedBrand }}">
                                @endif
                                @if ($search !== '')
                                    <input type="hidden" name="search" value="{{ $search }}">
                                @endif
                                @if (request()->filled('min_price'))
                                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                @endif

                                <span class="mr-2 text-sm font-medium">Sort By:</span>
                                <select name="sort" onchange="this.form.submit()"
                                    class="border rounded p-1 text-sm focus:outline-none focus:border-primary">
                                    <option value="featured" {{ $sort === 'featured' ? 'selected' : '' }}>Featured
                                    </option>
                                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to
                                        High</option>
                                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High
                                        to Low</option>
                                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="product-grid-view" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                    @foreach ($products as $product)


                        <div class="group">
                            <div class="relative overflow-hidden bg-gray-100 rounded-lg mb-4">
                                <a href="{{ route('shop.details', $product->slug) }}">
                                    <img src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }} "
                                        alt="Product"
                                        class="w-full h-[300px] object-cover transition duration-500 group-hover:scale-105" />
                                </a>
                                <div
                                    class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <button
                                        class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition">
                                        <i class="fa-regular fa-heart"></i>
                                    </button>
                                    <button
                                        class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                    </button>
                                    <button
                                        class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-center">
                                <h4 class="text-lg font-medium hover:text-primary"><a
                                        href="{{ route('shop.details', $product->slug) }}">{{ $product->name }}</a>
                                </h4>
                                <p class="text-primary font-bold mt-1">
                                    @if ($product->sale_price && $product->sale_price < $product->regular_price)
                                        <span class="text-xl text-gray-400 line-through">
                                            ${{ number_format($product->regular_price, 2) }}
                                        </span>
                                    @endif
                                    <span class="text-2xl text-primary font-bold">
                                        ${{ $product->sale_price ? number_format($product->sale_price, 2) : number_format($product->regular_price, 2) }}
                                    </span>

                                </p>
                            </div>
                        </div>

                    @endforeach

                </div>

                <div id="product-list-view" class="flex flex-col space-y-8 hidden">
                    @forelse ($products as $product)
                        @php
                            $hasSale = $product->sale_price && $product->sale_price < $product->regular_price;
                        @endphp
                        <div
                            class="flex flex-col md:flex-row gap-6 bg-white border rounded-lg p-4 hover:shadow-lg transition">
                            <div class="w-full md:w-1/3 relative bg-gray-100 rounded overflow-hidden">
                                <a href="{{ route('shop.details', $product->slug) }}">
                                    <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}"
                                        alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </a>
                            </div>
                            <div class="w-full md:w-2/3 flex flex-col justify-center">
                                <h4 class="text-xl font-bold hover:text-primary mb-2">
                                    <a href="{{ route('shop.details', $product->slug) }}">{{ $product->name }}</a>
                                </h4>
                                <p class="text-primary font-bold text-lg mb-4">
                                    @if ($hasSale)
                                        <span
                                            class="line-through text-gray-400 mr-2">${{ number_format($product->regular_price, 2) }}</span>
                                    @endif
                                    ${{ number_format($hasSale ? $product->sale_price : $product->regular_price, 2) }}
                                </p>
                                <p class="text-gray-600 mb-6 text-sm leading-relaxed">{{ $product->short_description }}</p>
                                <div class="flex space-x-2">
                                    <button class="px-4 py-2 border rounded hover:bg-primary hover:text-white transition"><i
                                            class="fa-regular fa-heart"></i></button>
                                    <button class="px-4 py-2 border rounded hover:bg-primary hover:text-white transition"><i
                                            class="fa-solid fa-bag-shopping"></i> Add to Cart</button>
                                    <button class="px-4 py-2 border rounded hover:bg-primary hover:text-white transition"><i
                                            class="fa-solid fa-magnifying-glass"></i></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No products found.</p>
                    @endforelse
                </div>

                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>



    <!-- Main Content End -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('category-filter');

            // Yenilənəndən sonra əvvəlki scroll mövqeyinə qayıt
            const saved = sessionStorage.getItem('shopScroll');
            if (saved !== null) {
                window.scrollTo(0, parseInt(saved));
                sessionStorage.removeItem('shopScroll');
            }

            // Kateqoriya seçiləndə mövqeyi yadda saxla və formu göndər
            document.querySelectorAll('.category-checkbox').forEach(function (cb) {
                cb.addEventListener('change', function () {
                    sessionStorage.setItem('shopScroll', window.scrollY);
                    form.submit();
                });
            });
        });

        const searchInput = document.getElementById('shop-search');
        const searchForm = document.getElementById('search-form');
        let searchTimer;

        if (searchInput && searchForm) {
            // Yenilənəndən sonra kursoru sona qoy
            if (searchInput.value) {
                searchInput.focus();
                const len = searchInput.value.length;
                searchInput.setSelectionRange(len, len);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    searchForm.submit();
                }, 500);
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            const rangeMin = document.getElementById('range-min');
            const rangeMax = document.getElementById('range-max');
            const minInput = document.getElementById('min-price-input');
            const maxInput = document.getElementById('max-price-input');
            const minDisplay = document.getElementById('price-min-display');
            const maxDisplay = document.getElementById('price-max-display');
            const track = document.getElementById('range-track');

            if (!rangeMin || !rangeMax) return;

            function update(changed) {
                let min = parseInt(rangeMin.value);
                let max = parseInt(rangeMax.value);

                // min max-dan böyük olmasın
                if (min > max) {
                    if (changed === 'min') {min = max; rangeMin.value = min;}
                    else {max = min; rangeMax.value = max;}
                }

                minInput.value = min;
                maxInput.value = max;
                minDisplay.textContent = min;
                maxDisplay.textContent = max;

                const lo = parseInt(rangeMin.min);
                const hi = parseInt(rangeMin.max);
                const span = (hi - lo) || 1;
                track.style.left = ((min - lo) / span * 100) + '%';
                track.style.right = (100 - (max - lo) / span * 100) + '%';
            }

            rangeMin.addEventListener('input', () => update('min'));
            rangeMax.addEventListener('input', () => update('max'));
            update();
        });
    </script>
</x-app-layout>