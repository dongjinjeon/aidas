<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantKycData extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    protected $casts = [
        'data'      => 'object',
    ];
}
