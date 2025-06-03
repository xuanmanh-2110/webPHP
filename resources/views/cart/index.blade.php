@extends('layouts.app')

@section('content')
<button onclick="window.history.back()" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">
  ← Quay lại trang trước
</button>
<div class="max-w-4xl mx-auto my-8 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Giỏ hàng</h2>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">{{ session('success') }}</div>
    @endif
    @if(empty($cart))
        <p class="text-gray-700 text-lg text-center">Giỏ hàng của bạn đang trống.</p>
    @else
    <form action="{{ route('cart.checkoutSelected') }}" method="POST">
        @csrf
        <div class="overflow-x-auto bg-white rounded-lg shadow-sm mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            <input type="checkbox" id="select-all" onclick="toggleAll(this)" class="form-checkbox h-4 w-4 text-rose-500 transition duration-150 ease-in-out">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Sản phẩm</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Giá</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Số lượng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Thành tiền</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
                    @foreach($cart as $id => $item)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_products[]" value="{{ $id }}" class="form-checkbox h-4 w-4 text-rose-500 transition duration-150 ease-in-out">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item['price'], 0, ',', '.') }} VND</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" data-id="{{ $id }}" class="quantity-input w-16 sm:w-20 p-1 border border-gray-300 rounded-md text-sm">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap total-price-{{ $id }}">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} VND</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" data-id="{{ $id }}" class="remove-item-btn bg-rose-500 hover:bg-rose-600 text-white font-bold py-1 px-3 rounded text-xs transition-colors duration-200">Xóa</button>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-right text-lg font-bold">Tổng cộng:</td>
                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-lg font-bold text-rose-600" id="cart-total">{{ number_format($total, 0, ',', '.') }} VND</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button type="submit" class="w-full bg-rose-500 text-white font-bold py-3 px-4 rounded-lg text-lg transition-colors duration-200 mb-4 hover:bg-rose-600">Thanh toán sản phẩm đã chọn</button>
    </form>

    <div id="toast-notification" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white text-gray-800 px-5 py-3 rounded-lg shadow-lg z-50 hidden transition-opacity duration-300 ease-out opacity-0 border border-gray-200">
        <p class="text-base font-semibold mb-2 text-center">Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?</p>
        <div class="flex justify-center gap-2 mt-2">
            <button id="toast-cancel-btn" class="px-3 py-1.5 bg-white text-gray-800 rounded-md text-xs hover:bg-gray-100 border border-gray-300 transition-colors duration-200">Hủy</button>
            <button id="toast-confirm-btn" class="px-3 py-1.5 bg-red-500 text-white rounded-md text-xs hover:bg-red-600 transition-colors duration-200">Xóa</button>
        </div>
    </div>

    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('selected_products[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toastNotification = document.getElementById('toast-notification');
            const toastConfirmBtn = document.getElementById('toast-confirm-btn');
            const toastCancelBtn = document.getElementById('toast-cancel-btn');
            let currentProductIdToDelete = null;

            function showToast() {
                toastNotification.classList.remove('hidden', 'opacity-0');
                toastNotification.classList.add('opacity-100');
            }

            function hideToast() {
                toastNotification.classList.remove('opacity-100');
                toastNotification.classList.add('opacity-0');
                setTimeout(() => {
                    toastNotification.classList.add('hidden');
                }, 300); // Match transition duration
            }

            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const productId = this.dataset.id;
                    const newQuantity = this.value;

                    fetch(`/cart/update/${productId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ quantity: newQuantity })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`.total-price-${productId}`).innerText = data.item_total_formatted;
                            document.getElementById('cart-total').innerText = data.cart_total_formatted;
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Đã xảy ra lỗi khi cập nhật số lượng.');
                    });
                });
            });

            document.querySelectorAll('.remove-item-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentProductIdToDelete = this.dataset.id;
                    showToast();
                });
            });

            toastConfirmBtn.addEventListener('click', function() {
                hideToast();
                if (currentProductIdToDelete) {
                    fetch(`/cart/remove/${currentProductIdToDelete}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const rowToRemove = document.querySelector(`.remove-item-btn[data-id="${currentProductIdToDelete}"]`).closest('tr');
                            if (rowToRemove) {
                                rowToRemove.remove();
                            }
                            document.getElementById('cart-total').innerText = data.cart_total_formatted;
                            if (data.cart_total_formatted === '0 VND') {
                                document.querySelector('.max-w-4xl.mx-auto.my-8.p-6.bg-white.rounded-lg.shadow-md').innerHTML = `
                                    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Giỏ hàng</h2>
                                    <p class="text-gray-700 text-lg text-center">Giỏ hàng của bạn đang trống.</p>
                                    <a href="{{ route('products.shop') }}" class="block text-center bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-3 px-4 rounded-lg text-lg transition-colors duration-200 mt-4">Tiếp tục mua hàng</a>
                                `;
                            }
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Đã xảy ra lỗi khi xóa sản phẩm.');
                    });
                }
            });

            toastCancelBtn.addEventListener('click', function() {
                hideToast();
                currentProductIdToDelete = null;
            });
        });
    </script>
    @endif
    <a href="{{ route('products.shop') }}" class="block text-center bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-3 px-4 rounded-lg text-lg transition-colors duration-200">Tiếp tục mua hàng</a>
</div>
@endsection