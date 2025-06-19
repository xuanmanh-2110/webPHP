<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['id', 'customer_id', 'total_amount', 'status', 'address', 'phone', 'payment_method', 'customer_bank_name', 'customer_account_number'];
    
    // Tắt auto increment để tự quản lý ID
    public $incrementing = false;
    protected $keyType = 'int';
    
    protected $dates = ['created_at', 'updated_at'];
    
    // Phương thức để hiển thị thời gian đầy đủ theo múi giờ Việt Nam
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
    }
    
    // Phương thức để hiển thị ngày ngắn gọn
    public function getFormattedDateAttribute()
    {
        return $this->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y');
    }

    // Các trạng thái đơn hàng
    public static function getStatuses()
    {
        return [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã giao',
            'delivered' => 'Đã nhận',
            'cancelled' => 'Đã hủy'
        ];
    }

    // Lấy tên trạng thái bằng tiếng Việt
    public function getStatusNameAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? 'Không xác định';
    }

    // Lấy màu sắc cho trạng thái
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Tìm ID thấp nhất có sẵn để sử dụng cho order mới
     */
    public static function getNextAvailableId()
    {
        // Lấy tất cả ID hiện tại, sắp xếp theo thứ tự tăng dần
        $existingIds = self::pluck('id')->sort()->values();
        
        // Nếu không có order nào, bắt đầu từ 1
        if ($existingIds->isEmpty()) {
            return 1;
        }
        
        // Tìm khoảng trống đầu tiên
        for ($i = 1; $i <= $existingIds->max() + 1; $i++) {
            if (!$existingIds->contains($i)) {
                return $i;
            }
        }
        
        // Fallback (không bao giờ xảy ra với logic trên)
        return $existingIds->max() + 1;
    }
    
    /**
     * Override phương thức boot để tự động gán ID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = self::getNextAvailableId();
            }
        });
    }
}
