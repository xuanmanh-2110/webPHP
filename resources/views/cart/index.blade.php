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

    <!-- Modal xác nhận xóa sản phẩm khỏi giỏ hàng -->
    <div id="confirm-modal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-rose-100 rounded-full">
                    <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Xác nhận xóa sản phẩm</h3>
                <p class="text-gray-600 text-center mb-6">Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?</p>
                <div class="flex space-x-3">
                    <button id="confirm-cancel" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        Hủy
                    </button>
                    <button id="confirm-delete" class="flex-1 px-4 py-2 bg-rose-600 text-white font-semibold rounded-lg hover:bg-rose-700 transition-colors duration-200">
                        Xóa
                    </button>
                </div>
            </div>
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
            const modal = document.getElementById('confirm-modal');
            const confirmBtn = document.getElementById('confirm-delete');
            const cancelBtn = document.getElementById('confirm-cancel');
            let currentProductIdToDelete = null;

            function showModal() {
                modal.classList.remove('hidden');
            }

            function hideModal() {
                modal.classList.add('hidden');
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
                    showModal();
                });
            });

            // Xử lý khi nhấn Hủy
            const handleCancel = () => {
                hideModal();
                currentProductIdToDelete = null;
                confirmBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
            };

            // Xử lý khi nhấn Xóa
            const handleConfirm = () => {
                hideModal();
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
                confirmBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
            };

            // Event listener để hiển thị modal khi nhấn nút xóa
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    currentProductIdToDelete = e.target.dataset.id;
                    showModal();
                    
                    // Thêm event listeners cho modal
                    confirmBtn.addEventListener('click', handleConfirm);
                    cancelBtn.addEventListener('click', handleCancel);
                }
            });
        });
    </script>
    @endif
    <a href="{{ route('products.shop') }}" class="block text-center bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-3 px-4 rounded-lg text-lg transition-colors duration-200">Tiếp tục mua hàng</a>
</div>
@endsection