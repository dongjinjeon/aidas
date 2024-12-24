<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantNotification extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    protected $casts = [
        'id' => 'integer', 
        'message'   => 'object',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
