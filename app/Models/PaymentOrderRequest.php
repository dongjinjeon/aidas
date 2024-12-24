<?php

namespace App\Models;

use App\Models\User;
use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentOrderRequest extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'data'  => 'object',
    ];
    public function merchant() {
        return $this->belongsTo(User::class,'merchant_id');
    }

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
}
