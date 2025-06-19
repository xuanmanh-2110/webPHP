@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Thông tin tài khoản</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Avatar Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Ảnh đại diện</h2>
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 shadow-lg">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('images/avatars/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-4xl text-gray-500">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Ảnh đại diện hiện tại</p>
                </div>
                
                <div class="flex-1 w-full">
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">Chọn ảnh mới</label>
                            <input type="file" id="avatar" name="avatar" accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="previewAvatar(this)">
                            <p class="text-xs text-gray-500 mt-1">Chỉ chấp nhận file: JPG, PNG, GIF. Kích thước tối đa: 2MB</p>
                            @error('avatar')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div id="avatar-preview" class="hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">Xem trước:</p>
                            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-gray-200">
                                <img id="preview-image" src="" alt="Preview" class="w-full h-full object-cover">
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                Cập nhật ảnh
                            </button>
                            @if(Auth::user()->avatar)
                            <button type="button" onclick="removeAvatar()" class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                                Xóa ảnh
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Thông tin cá nhân -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Thông tin cá nhân</h2>
                
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
                        <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nhập số điện thoại">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                            <!-- Province Dropdown -->
                            <div class="relative" id="province-select">
                                <button type="button" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-white text-left cursor-pointer flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="province-btn">
                                    <span id="province-text">Chọn Tỉnh/Thành phố</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" id="province-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-lg z-50 max-h-48 overflow-y-auto mt-1 hidden" id="province-dropdown">
                                    <!-- Options sẽ được load bằng JavaScript -->
                                </div>
                                <input type="hidden" name="province" id="province-input">
                            </div>

                            <!-- District Dropdown -->
                            <div class="relative" id="district-select">
                                <button type="button" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-gray-50 text-left cursor-not-allowed flex justify-between items-center text-gray-400" id="district-btn" disabled>
                                    <span id="district-text">Chọn Quận/Huyện</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" id="district-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-lg z-50 max-h-48 overflow-y-auto mt-1 hidden" id="district-dropdown">
                                    <!-- Options sẽ được load bằng JavaScript -->
                                </div>
                                <input type="hidden" name="district" id="district-input">
                            </div>

                            <!-- Ward Dropdown -->
                            <div class="relative" id="ward-select">
                                <button type="button" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-gray-50 text-left cursor-not-allowed flex justify-between items-center text-gray-400" id="ward-btn" disabled>
                                    <span id="ward-text">Chọn Phường/Xã</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" id="ward-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-lg z-50 max-h-48 overflow-y-auto mt-1 hidden" id="ward-dropdown">
                                    <!-- Options sẽ được load bằng JavaScript -->
                                </div>
                                <input type="hidden" name="ward" id="ward-input">
                            </div>
                        </div>
                        
                        <input type="text" id="street" name="street" placeholder="Số nhà, tên đường"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3">
                        
                        <textarea id="address" name="address" rows="2" readonly
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none"
                                  placeholder="Địa chỉ đầy đủ sẽ hiển thị ở đây">{{ old('address', Auth::user()->address) }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Cập nhật thông tin
                    </button>
                </form>
            </div>

            <!-- Thay đổi mật khẩu -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Thay đổi mật khẩu</h2>
                
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu hiện tại</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                        Thay đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>

        <!-- Thông tin tài khoản hiện tại -->
        <div class="bg-gray-50 rounded-lg p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông tin hiện tại</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600">Họ và tên:</span>
                    <p class="text-gray-800">{{ Auth::user()->name }}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Email:</span>
                    <p class="text-gray-800">{{ Auth::user()->email }}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Số điện thoại:</span>
                    <p class="text-gray-800">{{ Auth::user()->phone ?: 'Chưa cập nhật' }}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Địa chỉ:</span>
                    <p class="text-gray-800">{{ Auth::user()->address ?: 'Chưa cập nhật' }}</p>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Ngày tham gia:</span>
                    <p class="text-gray-800">{{ Auth::user()->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Nút quay lại -->
        <div class="mt-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại trang chủ
            </a>
        </div>
    </div>
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Custom dropdown elements
    const provinceBtn = document.getElementById('province-btn');
    const provinceDropdown = document.getElementById('province-dropdown');
    const provinceText = document.getElementById('province-text');
    const provinceInput = document.getElementById('province-input');
    const provinceSelect = document.getElementById('province-select');

    const districtBtn = document.getElementById('district-btn');
    const districtDropdown = document.getElementById('district-dropdown');
    const districtText = document.getElementById('district-text');
    const districtInput = document.getElementById('district-input');
    const districtSelect = document.getElementById('district-select');

    const wardBtn = document.getElementById('ward-btn');
    const wardDropdown = document.getElementById('ward-dropdown');
    const wardText = document.getElementById('ward-text');
    const wardInput = document.getElementById('ward-input');
    const wardSelect = document.getElementById('ward-select');

    const streetInput = document.getElementById('street');
    const addressTextarea = document.getElementById('address');

    let selectedProvince = null;
    let selectedDistrict = null;
    let selectedWard = null;

    // Generic function to handle dropdown toggle
    function toggleDropdown(dropdown, arrow) {
        // Close all other dropdowns
        document.querySelectorAll('[id$="-dropdown"]').forEach(d => {
            if (d !== dropdown) {
                d.classList.add('hidden');
            }
        });
        document.querySelectorAll('[id$="-arrow"]').forEach(a => {
            if (a !== arrow) {
                a.classList.remove('rotate-180');
            }
        });

        // Toggle current dropdown
        dropdown.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Load provinces from API
    async function loadProvinces() {
        try {
            const response = await fetch('https://provinces.open-api.vn/api/p/');
            const provinces = await response.json();
            
            provinceDropdown.innerHTML = '';
            provinces.forEach(province => {
                const option = document.createElement('div');
                option.className = 'px-3 py-2 cursor-pointer border-b border-gray-100 hover:bg-gray-50 text-gray-700';
                option.textContent = province.name;
                option.dataset.code = province.code;
                option.dataset.name = province.name;
                option.addEventListener('click', () => selectProvince(province));
                provinceDropdown.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    // Load districts by province code
    async function loadDistricts(provinceCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
            const province = await response.json();
            
            districtDropdown.innerHTML = '';
            districtBtn.disabled = false;
            districtBtn.className = 'w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-white text-left cursor-pointer flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
            
            province.districts.forEach(district => {
                const option = document.createElement('div');
                option.className = 'px-3 py-2 cursor-pointer border-b border-gray-100 hover:bg-gray-50 text-gray-700';
                option.textContent = district.name;
                option.dataset.code = district.code;
                option.dataset.name = district.name;
                option.addEventListener('click', () => selectDistrict(district));
                districtDropdown.appendChild(option);
            });
            
            // Reset ward
            resetWard();
        } catch (error) {
            console.error('Error loading districts:', error);
        }
    }

    // Load wards by district code
    async function loadWards(districtCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
            const district = await response.json();
            
            wardDropdown.innerHTML = '';
            wardBtn.disabled = false;
            wardBtn.className = 'w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-white text-left cursor-pointer flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
            
            district.wards.forEach(ward => {
                const option = document.createElement('div');
                option.className = 'px-3 py-2 cursor-pointer border-b border-gray-100 hover:bg-gray-50 text-gray-700';
                option.textContent = ward.name;
                option.dataset.code = ward.code;
                option.dataset.name = ward.name;
                option.addEventListener('click', () => selectWard(ward));
                wardDropdown.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading wards:', error);
        }
    }

    // Select province
    function selectProvince(province) {
        selectedProvince = province;
        provinceText.textContent = province.name;
        provinceInput.value = province.code;
        provinceDropdown.classList.add('hidden');
        document.getElementById('province-arrow').classList.remove('rotate-180');
        
        // Reset dependent dropdowns
        resetDistrict();
        resetWard();
        
        loadDistricts(province.code);
        updateFullAddress();
    }

    // Select district
    function selectDistrict(district) {
        selectedDistrict = district;
        districtText.textContent = district.name;
        districtInput.value = district.code;
        districtDropdown.classList.add('hidden');
        document.getElementById('district-arrow').classList.remove('rotate-180');
        
        // Reset ward
        resetWard();
        
        loadWards(district.code);
        updateFullAddress();
    }

    // Select ward
    function selectWard(ward) {
        selectedWard = ward;
        wardText.textContent = ward.name;
        wardInput.value = ward.code;
        wardDropdown.classList.add('hidden');
        document.getElementById('ward-arrow').classList.remove('rotate-180');
        
        updateFullAddress();
    }

    // Reset district
    function resetDistrict() {
        selectedDistrict = null;
        districtText.textContent = 'Chọn Quận/Huyện';
        districtInput.value = '';
        districtDropdown.innerHTML = '';
        districtBtn.disabled = true;
        districtBtn.className = 'w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-gray-50 text-left cursor-not-allowed flex justify-between items-center text-gray-400';
    }

    // Reset ward
    function resetWard() {
        selectedWard = null;
        wardText.textContent = 'Chọn Phường/Xã';
        wardInput.value = '';
        wardDropdown.innerHTML = '';
        wardBtn.disabled = true;
        wardBtn.className = 'w-full px-3 py-2 pr-10 border border-gray-300 rounded-md bg-gray-50 text-left cursor-not-allowed flex justify-between items-center text-gray-400';
    }

    // Update full address
    function updateFullAddress() {
        const street = streetInput.value.trim();
        const wardName = selectedWard ? selectedWard.name : '';
        const districtName = selectedDistrict ? selectedDistrict.name : '';
        const provinceName = selectedProvince ? selectedProvince.name : '';
        
        const addressParts = [street, wardName, districtName, provinceName].filter(part => part);
        addressTextarea.value = addressParts.join(', ');
    }

    // Event listeners for dropdown buttons
    provinceBtn.addEventListener('click', () => toggleDropdown(provinceDropdown, document.getElementById('province-arrow')));
    districtBtn.addEventListener('click', () => {
        if (!districtBtn.disabled) {
            toggleDropdown(districtDropdown, document.getElementById('district-arrow'));
        }
    });
    wardBtn.addEventListener('click', () => {
        if (!wardBtn.disabled) {
            toggleDropdown(wardDropdown, document.getElementById('ward-arrow'));
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
            document.querySelectorAll('[id$="-arrow"]').forEach(arrow => {
                arrow.classList.remove('rotate-180');
            });
        }
    });

    // Street input listener
    streetInput.addEventListener('input', updateFullAddress);

    // Parse existing address if available
    function parseExistingAddress() {
        const currentAddress = addressTextarea.value.trim();
        if (currentAddress) {
            streetInput.value = currentAddress;
        }
    }

    // Initialize
    loadProvinces();
    parseExistingAddress();
});

// Avatar functions
function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    const previewImage = document.getElementById('preview-image');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
    }
}

function removeAvatar() {
    if (confirm('Bạn có chắc muốn xóa ảnh đại diện?')) {
        fetch('{{ route("profile.avatar.remove") }}', {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại!');
        });
    }
}
</script>