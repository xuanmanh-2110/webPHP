<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;

class SyncCustomerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đồng bộ Customer với User dựa trên email
        $customers = Customer::whereNull('user_id')->get();
        
        foreach ($customers as $customer) {
            $user = User::where('email', $customer->email)->first();
            
            if ($user) {
                // Nếu tìm thấy user có email trùng, liên kết chúng
                $customer->user_id = $user->id;
                $customer->save();
                
                echo "Đã liên kết Customer #{$customer->id} với User #{$user->id} (email: {$customer->email})\n";
            } else {
                // Nếu không tìm thấy user, tạo user mới từ thông tin customer
                $newUser = User::create([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'password' => bcrypt('password123'), // mật khẩu mặc định
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                ]);
                
                $customer->user_id = $newUser->id;
                $customer->save();
                
                echo "Đã tạo User mới #{$newUser->id} và liên kết với Customer #{$customer->id} (email: {$customer->email})\n";
            }
        }
        
        echo "Hoàn thành đồng bộ dữ liệu Customer và User.\n";
    }
}
