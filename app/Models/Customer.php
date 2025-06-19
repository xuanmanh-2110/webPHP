<?php

namespace App\Models;
use App\Models\Order;


use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'user_id'];
    
    protected $dates = ['created_at', 'updated_at'];
    
    // Phương thức để hiển thị thời gian đầy đủ theo múi giờ Việt Nam
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
