<x-admin-layout>
    <main class="flex-1 overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add New Category</h1>
            <a href="{{ route('admin.categories') }}"
                class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Category
            </a>
        </div>
        <div class="max-w-3xl mx-auto">
            <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                        <input type="text" id="name" name="name" placeholder="e.g. Samsung"
                            class="w-full border px-4 py-2 rounded-lg outline-none focus:ring-1 focus:ring-primary"
                            value="{{ old('name') }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1"> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category Slug</label>
                        <input type="text" id="slug" name="slug" placeholder="samsung"
                            class="w-full border px-4 py-2 rounded-lg bg-gray-50 outline-none"
                            value="{{ old('slug') }}">
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1"> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block-text-sm font-medium text-gray-700 mb-1">
                        Parent Category
                    </label>
                    <select name="parent_id"
                        class="w-full border px-4 py-2 rounded-lg outline-none focus:ring-1 focus:ring-primary bg-white">
                        <option value="">
                            --Select Parent Cartegory--
                        </option>
                        @foreach ($parentCategories as $pcategory)
                            <option value="{{ $pcategory->id }}"
                                {{ old('parent_id') == $pcategory->id ? 'selected' : '' }}>
                                {{ $pcategory->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Logo *</label>
                    <div class="relative flex items-center justify-center w-full h-40">

                        <label for="category-image"
                            class="relative flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition overflow-hidden">

                            <div id="upload-content" class="flex flex-col items-center justify-center pt-5 pb-6 z-10">
                                <i class="fa-solid fa-image text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Upload category logo (PNG/JPG)</p>
                            </div>

                            <img id="image-preview"
                                class="hidden absolute inset-0 w-full h-full object-contain p-2 z-20 bg-white"
                                src="" alt="Logo Preview">

                            <input id="category-image" name="image" type="file" class="hidden"
                                accept="image/png, image/jpeg, image/jpg, image/webp" />
                        </label>

                        <button type="button" id="remove-logo-btn"
                            class="hidden absolute top-2 right-2 z-30 bg-white text-red-500 hover:text-white hover:bg-red-500 rounded-full w-8 h-8 flex items-center justify-center shadow-md border border-gray-200 transition-colors focus:outline-none"
                            title="Remove image">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="status" name="status"
                        class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary" value="1"
                        {{ old('status') ? 'checked' : '' }}>
                    <label for="status" class="text-sm text-gray-700">Set as Active Category</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.categories') }}"
                        class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition text-sm">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition text-sm font-medium shadow-sm">Save
                        Category</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        let slugEdited = false;

        function slugify(text) {
            const map = {
                'ə': 'e',
                'ı': 'i',
                'ö': 'o',
                'ü': 'u',
                'ç': 'c',
                'ş': 's',
                'ğ': 'g'
            };
            return text.toLowerCase()
                .replace(/[əıöüçşğ]/g, c => map[c])
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        nameInput.addEventListener('input', () => {
            if (!slugEdited) slugInput.value = slugify(nameInput.value);
        });
        slugInput.addEventListener('input', () => {
            slugEdited = true;
        });

        // Image preview
        const fileInput = document.getElementById('category-image');
        const preview = document.getElementById('image-preview');
        const removeBtn = document.getElementById('remove-logo-btn');

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (!file) return;
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            removeBtn.classList.remove('hidden');
        });

        removeBtn.addEventListener('click', () => {
            fileInput.value = '';
            preview.src = '';
            preview.classList.add('hidden');
            removeBtn.classList.add('hidden');
        });
    </script>

</x-admin-layout>
