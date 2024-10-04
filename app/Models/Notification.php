<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'message',
        'url',
        'related_id',
        'is_read',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'related_id');
    }
    public function getNotificationUrl()
    {
        switch ($this->type) {
            case 'product':
                return route('admin.products', ['product_id' => $this->related_id]);
            case 'order':
                return route('admin.order.details', ['order_id' => $this->related_id]);
            default:
                return '#';
        }
    }
}
