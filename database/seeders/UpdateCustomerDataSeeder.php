<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class UpdateCustomerDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        
        foreach ($customers as $customer) {
            if (empty($customer->phone)) {
                $customer->phone = '0' . rand(900000000, 999999999);
            }
            
            if (empty($customer->address)) {
                $addresses = [
                    '123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM',
                    '456 Đường Lê Văn Lương, Quận Thanh Xuân, Hà Nội',
                    '789 Đường Trần Hưng Đạo, Quận 1, TP.HCM',
                    '321 Đường Hoàng Hoa Thám, Quận Ba Đình, Hà Nội',
                    '654 Đường Nguyễn Thị Minh Khai, Quận 3, TP.HCM',
                    '987 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội'
                ];
                $customer->address = $addresses[array_rand($addresses)];
            }
            
            $customer->save();
        }
    }
}