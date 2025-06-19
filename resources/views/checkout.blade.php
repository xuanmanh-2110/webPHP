@extends('layouts.app')

@section('content')
<a href="http://127.0.0.1:8000/shop" class="inline-block bg-rose-100 hover:bg-rose-200 text-rose-600 font-bold py-2 px-4 rounded-md shadow-md transition-colors duration-200 mb-4 ml-4">← Quay lại cửa hàng</a>
<div class="max-w-3xl mx-auto my-10 p-6 bg-white rounded-lg">
    <h2 class="text-3xl font-bold text-rose-600 mb-6 text-center">Thanh toán đơn hàng</h2>
    <form action="{{ route('checkout.process') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <!-- Hidden field để xác định đây có phải là "Mua ngay" không -->
        <input type="hidden" name="is_buy_now" value="{{ $isBuyNow ? '1' : '0' }}">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-1">Họ tên khách hàng:</label>
            <input type="text" value="{{ $user->name }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" disabled>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Địa chỉ giao hàng <span class="text-rose-500">*</span>:</label>
            
            <!-- Chọn tỉnh/thành phố -->
            <div class="mb-3">
                <label class="block text-gray-600 text-xs font-medium mb-1">Tỉnh/Thành phố:</label>
                <select id="city" name="city" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
                    <option value="">-- Đang tải... --</option>
                </select>
            </div>

            <!-- Chọn quận/huyện -->
            <div class="mb-3">
                <label class="block text-gray-600 text-xs font-medium mb-1">Quận/Huyện:</label>
                <select id="district" name="district" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
                    <option value="">-- Chọn quận/huyện --</option>
                </select>
            </div>

            <!-- Chọn xã/phường -->
            <div class="mb-3">
                <label class="block text-gray-600 text-xs font-medium mb-1">Xã/Phường:</label>
                <select id="ward" name="ward" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
                    <option value="">-- Chọn xã/phường --</option>
                </select>
            </div>

            <!-- Nhập địa chỉ chi tiết -->
            <div class="mb-3">
                <label class="block text-gray-600 text-xs font-medium mb-1">Số nhà, tên đường:</label>
                <input type="text" id="street" name="street" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" placeholder="Ví dụ: 123 Đường Nguyễn Văn A" required>
            </div>

            <!-- Hidden field để lưu địa chỉ đầy đủ -->
            <input type="hidden" id="full_address" name="address">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-1">Số điện thoại <span class="text-rose-500">*</span>:</label>
            <input type="text" name="phone" value="{{ $userPhone ?? $lastOrder->phone ?? '' }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring focus:ring-rose-200 focus:ring-opacity-50" required>
        </div>
        
        @if($userAddress || $userPhone)
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
            <p class="text-sm text-green-700">
                <i class="fas fa-info-circle mr-1"></i>
                Thông tin giao hàng được lấy từ hồ sơ cá nhân của bạn. Bạn có thể chỉnh sửa nếu muốn thay đổi.
            </p>
        </div>
        @elseif($lastOrder)
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
            <p class="text-sm text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                Thông tin giao hàng được lưu từ đơn hàng gần nhất. Bạn có thể chỉnh sửa nếu muốn thay đổi.
            </p>
        </div>
        @endif
        <h4 class="text-xl font-semibold text-gray-800 mb-4">Giỏ hàng của bạn</h4>
        <div class="overflow-x-auto mb-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cart as $id => $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item['quantity'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item['price'], 2) }}₫</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item['price'] * $item['quantity'], 2) }}₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right text-xl font-bold text-rose-600 mb-6">
            <strong>Tổng cộng: {{ number_format($total, 2) }}₫</strong>
        </div>
        
        <!-- Phương thức thanh toán -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Phương thức thanh toán <span class="text-rose-500">*</span></h4>
            <div class="space-y-3">
                <!-- Ship COD -->
                <div class="border border-gray-300 rounded-lg p-4 hover:border-rose-300 transition-colors duration-200">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" class="mt-1 text-rose-500 focus:ring-rose-500" required>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">🚚</span>
                                <span class="font-medium text-gray-900">Thanh toán khi nhận hàng (COD)</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Thanh toán bằng tiền mặt khi nhận hàng tại địa chỉ giao hàng</p>
                        </div>
                    </label>
                </div>
                
                <!-- Chuyển khoản ngân hàng -->
                <div class="border border-gray-300 rounded-lg p-4 hover:border-rose-300 transition-colors duration-200">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="bank_transfer" class="mt-1 text-rose-500 focus:ring-rose-500" required>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">🏦</span>
                                <span class="font-medium text-gray-900">Chuyển khoản ngân hàng</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Chuyển khoản trước khi giao hàng</p>
                            <div id="bank-fields" class="mt-3 space-y-3 hidden">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên ngân hàng <span class="text-rose-500">*</span></label>
                                    <input type="text" name="customer_bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent" placeholder="Ví dụ: Vietcombank, BIDV, Techcombank...">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản <span class="text-rose-500">*</span></label>
                                    <input type="text" name="customer_account_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent" placeholder="Nhập số tài khoản của bạn">
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <button type="submit" class="w-full py-3 px-8 bg-rose-500 text-white border-none rounded-md text-lg cursor-pointer hover:bg-rose-600 transition-colors duration-200">Xác nhận đặt hàng</button>
    </form>
</div>

<script>
// API URL cho Vietnam Administrative Units
const API_BASE_URL = 'https://provinces.open-api.vn/api';

// Biến lưu trữ dữ liệu
let provincesData = [];
let districtsData = [];
let wardsData = [];

// Dữ liệu từ user profile (ưu tiên) hoặc đơn hàng gần nhất
const userAddress = @json($userAddress ?? '');
const lastOrderAddress = @json($lastOrder->address ?? '');
const preferredAddress = userAddress || lastOrderAddress;

// Load danh sách tỉnh/thành phố khi trang load
document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
});

// Hàm parse địa chỉ từ string
function parseAddress(addressString) {
    if (!addressString) return null;
    
    const parts = addressString.split(', ');
    if (parts.length >= 3) {
        return {
            street: parts[0],
            ward: parts[1] || '',
            district: parts[2] || '',
            city: parts[3] || ''
        };
    }
    return null;
}

// Hàm load danh sách tỉnh/thành phố
async function loadProvinces() {
    try {
        const response = await fetch(`${API_BASE_URL}/p/`);
        const data = await response.json();
        provincesData = data;
        
        const citySelect = document.getElementById('city');
        citySelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
        
        data.forEach(function(province) {
            const option = document.createElement('option');
            option.value = province.code;
            option.textContent = province.name;
            option.setAttribute('data-name', province.name);
            citySelect.appendChild(option);
        });
        
        // Load địa chỉ từ user profile hoặc đơn hàng gần nhất
        if (preferredAddress) {
            const parsedAddress = parseAddress(preferredAddress);
            if (parsedAddress) {
                await loadPreviousAddress(parsedAddress);
            }
        }
    } catch (error) {
        console.error('Lỗi khi load danh sách tỉnh/thành phố:', error);
        // Fallback về danh sách cứng nếu API không hoạt động
        loadFallbackProvinces();
    }
}

// Hàm load địa chỉ từ đơn hàng trước
async function loadPreviousAddress(parsedAddress) {
    // Tìm và chọn thành phố
    const citySelect = document.getElementById('city');
    for (let option of citySelect.options) {
        if (option.getAttribute('data-name') === parsedAddress.city) {
            option.selected = true;
            
            // Load quận/huyện
            await loadDistricts(option.value);
            
            // Tìm và chọn quận/huyện
            setTimeout(async () => {
                const districtSelect = document.getElementById('district');
                for (let districtOption of districtSelect.options) {
                    if (districtOption.getAttribute('data-name') === parsedAddress.district) {
                        districtOption.selected = true;
                        
                        // Load xã/phường
                        await loadWards(districtOption.value);
                        
                        // Tìm và chọn xã/phường
                        setTimeout(() => {
                            const wardSelect = document.getElementById('ward');
                            for (let wardOption of wardSelect.options) {
                                if (wardOption.getAttribute('data-name') === parsedAddress.ward) {
                                    wardOption.selected = true;
                                    break;
                                }
                            }
                            
                            // Fill địa chỉ chi tiết
                            document.getElementById('street').value = parsedAddress.street;
                            updateFullAddress();
                        }, 500);
                        break;
                    }
                }
            }, 500);
            break;
        }
    }
}

// Hàm load danh sách quận/huyện theo tỉnh
async function loadDistricts(provinceCode) {
    try {
        const response = await fetch(`${API_BASE_URL}/p/${provinceCode}?depth=2`);
        const data = await response.json();
        districtsData = data.districts || [];
        
        const districtSelect = document.getElementById('district');
        districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
        
        data.districts.forEach(function(district) {
            const option = document.createElement('option');
            option.value = district.code;
            option.textContent = district.name;
            option.setAttribute('data-name', district.name);
            districtSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Lỗi khi load danh sách quận/huyện:', error);
        // Fallback nếu API lỗi
        const districtSelect = document.getElementById('district');
        districtSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu, vui lòng thử lại --</option>';
    }
}

// Hàm load danh sách xã/phường theo quận/huyện
async function loadWards(districtCode) {
    try {
        const response = await fetch(`${API_BASE_URL}/d/${districtCode}?depth=2`);
        const data = await response.json();
        wardsData = data.wards || [];
        
        const wardSelect = document.getElementById('ward');
        wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
        
        data.wards.forEach(function(ward) {
            const option = document.createElement('option');
            option.value = ward.code;
            option.textContent = ward.name;
            option.setAttribute('data-name', ward.name);
            wardSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Lỗi khi load danh sách xã/phường:', error);
        // Fallback nếu API lỗi
        const wardSelect = document.getElementById('ward');
        wardSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu, vui lòng thử lại --</option>';
    }
}

// Hàm fallback nếu API không hoạt động
function loadFallbackProvinces() {
    const provinces = [
        {code: '01', name: 'Hà Nội'},
        {code: '79', name: 'TP. Hồ Chí Minh'},
        {code: '48', name: 'Đà Nẵng'},
        {code: '31', name: 'Hải Phòng'},
        {code: '92', name: 'Cần Thơ'},
        {code: '89', name: 'An Giang'},
        {code: '77', name: 'Bà Rịa - Vũng Tàu'},
        {code: '24', name: 'Bắc Giang'},
        {code: '06', name: 'Bắc Kạn'},
        {code: '95', name: 'Bạc Liêu'},
        {code: '27', name: 'Bắc Ninh'},
        {code: '83', name: 'Bến Tre'},
        {code: '52', name: 'Bình Định'},
        {code: '74', name: 'Bình Dương'},
        {code: '70', name: 'Bình Phước'},
        {code: '60', name: 'Bình Thuận'},
        {code: '96', name: 'Cà Mau'},
        {code: '04', name: 'Cao Bằng'},
        {code: '66', name: 'Đắk Lắk'},
        {code: '67', name: 'Đắk Nông'},
        {code: '11', name: 'Điện Biên'},
        {code: '75', name: 'Đồng Nai'},
        {code: '87', name: 'Đồng Tháp'},
        {code: '64', name: 'Gia Lai'},
        {code: '02', name: 'Hà Giang'},
        {code: '35', name: 'Hà Nam'},
        {code: '42', name: 'Hà Tĩnh'},
        {code: '30', name: 'Hải Dương'},
        {code: '93', name: 'Hậu Giang'},
        {code: '17', name: 'Hòa Bình'},
        {code: '33', name: 'Hưng Yên'},
        {code: '56', name: 'Khánh Hòa'},
        {code: '91', name: 'Kiên Giang'},
        {code: '62', name: 'Kon Tum'},
        {code: '12', name: 'Lai Châu'},
        {code: '68', name: 'Lâm Đồng'},
        {code: '20', name: 'Lạng Sơn'},
        {code: '10', name: 'Lào Cai'},
        {code: '80', name: 'Long An'},
        {code: '36', name: 'Nam Định'},
        {code: '40', name: 'Nghệ An'},
        {code: '37', name: 'Ninh Bình'},
        {code: '58', name: 'Ninh Thuận'},
        {code: '25', name: 'Phú Thọ'},
        {code: '54', name: 'Phú Yên'},
        {code: '44', name: 'Quảng Bình'},
        {code: '49', name: 'Quảng Nam'},
        {code: '51', name: 'Quảng Ngãi'},
        {code: '22', name: 'Quảng Ninh'},
        {code: '45', name: 'Quảng Trị'},
        {code: '94', name: 'Sóc Trăng'},
        {code: '14', name: 'Sơn La'},
        {code: '72', name: 'Tây Ninh'},
        {code: '34', name: 'Thái Bình'},
        {code: '19', name: 'Thái Nguyên'},
        {code: '38', name: 'Thanh Hóa'},
        {code: '46', name: 'Thừa Thiên Huế'},
        {code: '82', name: 'Tiền Giang'},
        {code: '84', name: 'Trà Vinh'},
        {code: '08', name: 'Tuyên Quang'},
        {code: '86', name: 'Vĩnh Long'},
        {code: '26', name: 'Vĩnh Phúc'},
        {code: '15', name: 'Yên Bái'}
    ];
    
    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
    
    provinces.forEach(function(province) {
        const option = document.createElement('option');
        option.value = province.code;
        option.textContent = province.name;
        option.setAttribute('data-name', province.name);
        citySelect.appendChild(option);
    });
}

// Xử lý khi chọn tỉnh/thành phố
document.getElementById('city').addEventListener('change', function() {
    const provinceCode = this.value;
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    
    // Clear quận/huyện và xã/phường
    districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
    wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
    
    if (provinceCode) {
        districtSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        loadDistricts(provinceCode);
    }
    
    updateFullAddress();
});

// Xử lý khi chọn quận/huyện
document.getElementById('district').addEventListener('change', function() {
    const districtCode = this.value;
    const wardSelect = document.getElementById('ward');
    
    // Clear xã/phường
    wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
    
    if (districtCode) {
        wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        loadWards(districtCode);
    }
    
    updateFullAddress();
});

// Xử lý khi chọn xã/phường
document.getElementById('ward').addEventListener('change', updateFullAddress);

// Xử lý khi nhập địa chỉ chi tiết
document.getElementById('street').addEventListener('input', updateFullAddress);

// Hàm cập nhật địa chỉ đầy đủ
function updateFullAddress() {
    const street = document.getElementById('street').value.trim();
    const wardSelect = document.getElementById('ward');
    const districtSelect = document.getElementById('district');
    const citySelect = document.getElementById('city');
    
    const wardName = wardSelect.options[wardSelect.selectedIndex]?.getAttribute('data-name') || '';
    const districtName = districtSelect.options[districtSelect.selectedIndex]?.getAttribute('data-name') || '';
    const cityName = citySelect.options[citySelect.selectedIndex]?.getAttribute('data-name') || '';
    
    let fullAddress = '';
    if (street) {
        fullAddress += street;
    }
    if (wardName) {
        fullAddress += (fullAddress ? ', ' : '') + wardName;
    }
    if (districtName) {
        fullAddress += (fullAddress ? ', ' : '') + districtName;
    }
    if (cityName) {
        fullAddress += (fullAddress ? ', ' : '') + cityName;
    }
    
    document.getElementById('full_address').value = fullAddress;
}

// Xử lý phương thức thanh toán
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const bankFields = document.getElementById('bank-fields');
        const bankNameField = document.querySelector('input[name="customer_bank_name"]');
        const accountNumberField = document.querySelector('input[name="customer_account_number"]');
        
        if (this.value === 'bank_transfer') {
            bankFields.classList.remove('hidden');
            
            // Thêm required cho các trường ngân hàng
            bankNameField.required = true;
            accountNumberField.required = true;
        } else {
            bankFields.classList.add('hidden');
            // Bỏ required cho các trường ngân hàng
            bankNameField.required = false;
            accountNumberField.required = false;
            bankNameField.value = '';
            accountNumberField.value = '';
        }
    });
});

// Xử lý khi submit form
document.querySelector('form').addEventListener('submit', function(e) {
    const city = document.getElementById('city').value;
    const district = document.getElementById('district').value;
    const ward = document.getElementById('ward').value;
    const street = document.getElementById('street').value.trim();
    const phone = document.querySelector('input[name="phone"]').value.trim();
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (!city || !district || !ward || !street) {
        e.preventDefault();
        alert('Vui lòng nhập đầy đủ thông tin địa chỉ (tỉnh/thành phố, quận/huyện, xã/phường, số nhà/đường)!');
        return false;
    }
    
    if (!phone) {
        e.preventDefault();
        alert('Vui lòng nhập số điện thoại!');
        return false;
    }
    
    if (!paymentMethod) {
        e.preventDefault();
        alert('Vui lòng chọn phương thức thanh toán!');
        return false;
    }
    
    // Kiểm tra thông tin ngân hàng nếu chọn chuyển khoản
    if (paymentMethod.value === 'bank_transfer') {
        const bankName = document.querySelector('input[name="customer_bank_name"]').value.trim();
        const accountNumber = document.querySelector('input[name="customer_account_number"]').value.trim();
        
        if (!bankName) {
            e.preventDefault();
            alert('Vui lòng nhập tên ngân hàng!');
            return false;
        }
        
        if (!accountNumber) {
            e.preventDefault();
            alert('Vui lòng nhập số tài khoản!');
            return false;
        }
    }
    
    updateFullAddress(); // Đảm bảo địa chỉ đầy đủ được cập nhật
});
</script>
@endsection