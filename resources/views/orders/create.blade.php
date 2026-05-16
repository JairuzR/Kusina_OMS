@extends('layouts.app')

@section('title', 'New Order - KusinaOMS')
@section('page-title', 'New Order')

@section('content')
<div class="mt-4">
    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Order Details + Menu --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Order Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Order Details</h3>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <option value="dine_in">Dine In</option>
                                <option value="takeout">Takeout</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Table</label>
                            <select name="table_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <option value="">No table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->capacity }} seats)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Guests</label>
                            <input type="number" name="guests" value="1" min="1"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                  placeholder="Special instructions..."></textarea>
                    </div>
                </div>

                {{-- Menu Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Select Items</h3>

                    {{-- Category Tabs --}}
                    <div class="flex gap-2 overflow-x-auto pb-2 mb-4">
                        @foreach($categories as $index => $category)
                        <button type="button"
                                onclick="showCategory('category-{{ $category->id }}')"
                                id="tab-{{ $category->id }}"
                                class="category-tab px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap border transition
                                {{ $index === 0 ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-300 hover:border-orange-400' }}">
                            {{ $category->name }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Items Grid --}}
                    @foreach($categories as $index => $category)
                    <div id="category-{{ $category->id }}"
                         class="category-items {{ $index === 0 ? '' : 'hidden' }}">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($category->items as $item)
                            <button type="button"
                                    onclick="addItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})"
                                    class="text-left p-3 border border-gray-200 rounded-lg hover:border-orange-400 hover:bg-orange-50 transition">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                         class="w-full h-20 object-cover rounded mb-2">
                                @else
                                    <div class="w-full h-20 bg-gray-100 rounded mb-2 flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">No image</span>
                                    </div>
                                @endif
                                <p class="font-medium text-gray-800 text-xs">{{ $item->name }}</p>
                                <p class="text-orange-500 text-xs font-semibold">₱{{ number_format($item->price, 2) }}</p>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

            {{-- Right: Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-20">
                    <h3 class="font-semibold text-gray-800 mb-4">Order Summary</h3>

                    <div id="orderItems" class="space-y-2 mb-4 min-h-16">
                        <p id="emptyMessage" class="text-sm text-gray-400 text-center py-4">No items added yet.</p>
                    </div>

                    <div class="border-t border-gray-100 pt-3 space-y-1">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span id="subtotal">₱0.00</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tax (12%)</span>
                            <span id="tax">₱0.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-800 text-base pt-1 border-t border-gray-100">
                            <span>Total</span>
                            <span id="total">₱0.00</span>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-4 w-full bg-orange-500 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-orange-600 transition">
                        Place Order
                    </button>
                    <a href="{{ route('orders.index') }}"
                       class="mt-2 block text-center text-sm text-gray-500 hover:text-gray-700">
                        Cancel
                    </a>
                </div>
            </div>

        </div>

        {{-- Hidden items container --}}
        <div id="hiddenItems"></div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    let items = {};
    const TAX_RATE = 0.12;

    function showCategory(id) {
        document.querySelectorAll('.category-items').forEach(el => el.classList.add('hidden'));
        document.getElementById(id).classList.remove('hidden');

        document.querySelectorAll('.category-tab').forEach(btn => {
            btn.classList.remove('bg-orange-500', 'text-white', 'border-orange-500');
            btn.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
        });

        const tabId = 'tab-' + id.replace('category-', '');
        const tab = document.getElementById(tabId);
        if (tab) {
            tab.classList.add('bg-orange-500', 'text-white', 'border-orange-500');
            tab.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
        }
    }

    function addItem(id, name, price) {
        if (items[id]) {
            items[id].quantity++;
        } else {
            items[id] = { id, name, price, quantity: 1 };
        }
        renderItems();
    }

    function removeItem(id) {
        delete items[id];
        renderItems();
    }

    function changeQty(id, delta) {
        if (!items[id]) return;
        items[id].quantity += delta;
        if (items[id].quantity <= 0) delete items[id];
        renderItems();
    }

    function renderItems() {
        const container  = document.getElementById('orderItems');
        const hidden     = document.getElementById('hiddenItems');
        const emptyMsg   = document.getElementById('emptyMessage');
        const keys       = Object.keys(items);

        hidden.innerHTML = '';
        container.innerHTML = '';

        if (keys.length === 0) {
            container.appendChild(Object.assign(document.createElement('p'), {
                className: 'text-sm text-gray-400 text-center py-4',
                textContent: 'No items added yet.'
            }));
            updateTotals(0);
            return;
        }

        let subtotal = 0;

        keys.forEach((id, index) => {
            const item = items[id];
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;

            // Visible row
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between py-1.5 border-b border-gray-50';
            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-800 truncate">${item.name}</p>
                    <p class="text-xs text-orange-500">₱${item.price.toFixed(2)}</p>
                </div>
                <div class="flex items-center gap-1 ml-2">
                    <button type="button" onclick="changeQty(${id}, -1)"
                            class="w-5 h-5 rounded bg-gray-100 text-gray-600 text-xs flex items-center justify-center hover:bg-gray-200">-</button>
                    <span class="text-xs w-5 text-center">${item.quantity}</span>
                    <button type="button" onclick="changeQty(${id}, 1)"
                            class="w-5 h-5 rounded bg-gray-100 text-gray-600 text-xs flex items-center justify-center hover:bg-gray-200">+</button>
                    <button type="button" onclick="removeItem(${id})"
                            class="w-5 h-5 rounded bg-red-100 text-red-500 text-xs flex items-center justify-center hover:bg-red-200 ml-1">×</button>
                </div>
            `;
            container.appendChild(row);

            // Hidden inputs
            hidden.innerHTML += `
                <input type="hidden" name="items[${index}][menu_item_id]" value="${id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `;
        });

        updateTotals(subtotal);
    }

    function updateTotals(subtotal) {
        const tax   = subtotal * TAX_RATE;
        const total = subtotal + tax;
        document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
        document.getElementById('tax').textContent      = '₱' + tax.toFixed(2);
        document.getElementById('total').textContent    = '₱' + total.toFixed(2);
    }
</script>
@endpush