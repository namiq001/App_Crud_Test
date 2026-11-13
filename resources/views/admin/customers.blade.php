<x-admin-layout>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-100">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Customers</h1>
            <p class="text-sm text-gray-500">Manage your registered customers</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form id="filter-form" method="GET" action="{{ route('admin.customers') }}">
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </span>
                    <input type="text" id="search-input" name="search" value="{{ request('search') }}"
                        autocomplete="off" placeholder="Search name or email..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">

                @if (session('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-2">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 text-red-700 px-4 py-2">{{ session('error') }}</div>
                @endif

                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Mobile</th>
                            <th class="px-6 py-4">Registered</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-gray-800 text-sm">{{ $customer->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->mobile }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->created_at?->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button"
                                        onclick="deleteCustomer(@js($customer->name), {{ $customer->id }})"
                                        class="w-8 h-8 rounded-full hover:bg-gray-100 text-red-500 transition inline-flex items-center justify-center"
                                        title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $customer->id }}" method="POST"
                                        action="{{ route('admin.customer.delete', $customer->id) }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-solid fa-users text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium text-gray-900">No customers found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
        </div>
    </main>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Delete
                                    Product
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Are you sure you want to delete <strong
                                            id="delete-customer-name" class="text-gray-800">this product</strong>?
                                        All
                                        of
                                        its
                                        data will be permanently removed. This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="confirmDeleteBtn"
                            class="inline-flex w-full justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition">Delete</button>
                        <button type="button" id="cancelDeleteBtn"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-input');
            const filterForm = document.getElementById('filter-form');
            let timer;

            if (!searchInput || !filterForm) return;

            if (searchInput.value) {
                searchInput.focus();
                const len = searchInput.value.length;
                searchInput.setSelectionRange(len, len);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(() => filterForm.submit(), 400);
            });
        });

        const deleteModal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const cancelBtn = document.getElementById('cancelDeleteBtn');
        const customerNameSpan = document.getElementById('delete-customer-name');

        let customerIdToDelete = null;

        function deleteCustomer(name, id) {
            customerIdToDelete = id;
            customerNameSpan.textContent = name || 'this customer';
            deleteModal.classList.remove('hidden');
        }

        function closeModal() {
            deleteModal.classList.add('hidden');
            customerIdToDelete = null;
        }

        cancelBtn.addEventListener('click', closeModal);

        deleteModal.addEventListener('click', function (e) {
            if (e.target === this || e.target.classList.contains('bg-opacity-75')) {
                closeModal();
            }
        });

        confirmBtn.addEventListener('click', function () {
            if (customerIdToDelete) {
                const form = document.getElementById('delete-form-' + customerIdToDelete);
                if (form) form.submit();
            }
        });
    </script>
</x-admin-layout>