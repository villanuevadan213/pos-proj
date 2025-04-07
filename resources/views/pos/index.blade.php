<x-layout>
    <style>
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
    </style>

    <x-slot name="header">
        POS Page
    </x-slot>

    <div class="space-y-4">
        @if(session('success') || session('error'))
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="bg-red-500 text-white p-4 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
        @endif

        <div class="w-full flex justify-between items-start gap-4">
            <div class="w-1/2 h-[500px] flex flex-col space-y-4 border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-gray-800">Cart Summary</h3>
                <div id="cart-items" class="space-y-4 overflow-auto flex-grow">
                    <table class="w-full border-collapse border border-gray-200">
                        <thead class="sticky top-0 bg-gray-300 z-10">
                            <tr class="bg-gray-300 border-b border-white">
                                <th class="w-6/12 border border-white px-4 py-2 text-left">Name</th>
                                <th class="w-1/12 border border-white px-4 py-2 text-right">Qty</th>
                                <th class="w-2/12 border border-white px-4 py-2 text-right">Price</th>
                                <th class="w-1/12 border border-white px-4 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <td class="border border-gray-300 px-4 py-2 text-center font-bold" colspan="4">Your cart
                                is empty.
                            </td>
                        </tbody>
                    </table>
                </div>
                <button onclick="confirmClearCart()"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4 w-full">
                    Clear Cart
                </button>
            </div>
            <div class="w-1/2 h-[500px] flex flex-col space-y-4 border border-gray-200 rounded-lg p-4">
                <form method="POST" action="/pos" class="space-y-4 flex flex-col h-full">
                    <h3 class="text-lg font-bold text-gray-800">Payment Method</h3>
                    @csrf
                    <div class="space-y-2 mt-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="cash" checked
                                onchange="updatePaymentMethod(this.value)"
                                class="form-radio text-blue-500 focus:ring-blue-500">
                            <span>Cash</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="credit_card"
                                onchange="updatePaymentMethod(this.value)"
                                class="form-radio text-blue-500 focus:ring-blue-500">
                            <span>Credit Card</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="payment_method" value="debit_card"
                                onchange="updatePaymentMethod(this.value)"
                                class="form-radio text-blue-500 focus:ring-blue-500">
                            <span>Debit Card</span>
                        </label>
                    </div>

                    <div class="flex-grow"></div>

                    <div>
                        <h4 class="text-lg font-bold text-gray-800 flex justify-between">
                            <span>Total:</span>
                            <p><span id="cart-total"> 0.00</span></p>
                        </h4>
                    </div>

                    <input type="hidden" id="payment-method-field" name="payment_method" value="cash">
                    <input type="hidden" id="cart-data" name="cart" value="">

                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full mt-4">
                        Checkout
                    </button>
                </form>
            </div>
        </div>

        <div class="w-full grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4 flex-grow">
            @foreach ($items as $item)
                <div role="button" class="p-4 bg-white shadow rounded-lg flex flex-col space-y-2 h-full"
                    onclick="addToCart({{ $item->id }}, '{{ $item->name }}', {{ $item->price }}, {{ $item->quantity }})">
                    <h3 class="text-lg font-bold text-gray-800">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500">₱ {{ number_format($item->price, 2) }}</p>
                    <p class="text-sm text-gray-500">{{ $item->quantity }}</p>
                </div>
            @endforeach

            <div class="col-span-1 sm:col-span-3 md:col-span-5">
                {{ $items->links() }}
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function formatPrice(price) {
            return price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function addToCart(id, name, price, availableQuantity) {
            let quantityInput = document.getElementById(`quantity-${id}`);
            let quantity = quantityInput ? parseInt(quantityInput.value) : 1;

            if (quantity > availableQuantity) {
                alert(`You can only add up to ${availableQuantity} of this item.`);
                return;
            }

            let existingItemIndex = cart.findIndex(item => item.id === id);

            if (existingItemIndex !== -1) {
                let newQuantity = cart[existingItemIndex].quantity + quantity;
                if (newQuantity > availableQuantity) {
                    alert(`You cannot add more than ${availableQuantity} of this item.`);
                    return;
                }
                cart[existingItemIndex].quantity = newQuantity;
            } else {
                cart.push({ id, name, price, quantity });
            }

            updateCartUI();
        }

        function updateCartUI() {
            let cartItemsContainer = document.getElementById('cart-items');
            let cartBody = cartItemsContainer.querySelector('tbody');
            cartBody.innerHTML = '';

            if (cart.length === 0) {
                cartBody.innerHTML = `
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center font-bold" colspan="4">Your cart is empty.</td>
                    </tr>
                `;
            } else {
                cart.forEach(item => {
                    let row = document.createElement('tr');
                    row.classList.add('border-b', 'border-gray-300');
                    row.innerHTML = `
                        <td class="border border-gray-300 px-4 py-2">${item.name}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${item.quantity}</td>
                        <td class="px-4 py-2 flex justify-between"><div>₱</div> <div>${formatPrice(item.price)}</div></td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button onclick="confirmRemoveFromCart(${item.id})" class="text-red-500 hover:text-red-700">Delete</button>
                        </td>
                    `;
                    cartBody.appendChild(row);
                });
            }

            updateCartTotal();
        }

        function updateCartTotal() {
            let total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('cart-total').textContent = `₱ ${formatPrice(total)}`;
            document.getElementById('cart-data').value = JSON.stringify(cart); // Update the cart data
        }

        function confirmRemoveFromCart(id) {
            let confirmation = prompt("To confirm deletion, please type 'VOID'.");
            if (confirmation === 'VOID') {
                removeFromCart(id);
            } else {
                alert("Item not deleted.");
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCartUI();
        }

        function confirmClearCart() {
            if (cart.length === 0) {
                alert("Your cart is empty. No need to clear.");
                return;
            }

            let confirmation = prompt("To clear the cart, please type 'VOID'.");
            if (confirmation === 'VOID') {
                clearCart();
            } else {
                alert("Cart not cleared.");
            }
        }

        function clearCart() {
            cart = [];
            updateCartUI();
        }

        function updatePaymentMethod(value) {
            document.getElementById('payment-method-field').value = value;
        }
    </script>
</x-layout>