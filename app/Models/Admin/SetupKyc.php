<?php

namespace App\Models\Admin;

use App\Constants\GlobalConst;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetupKyc extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'fields'    => "object",
    ];

    public function scopeUserKyc($query) {
        return $query->where("user_type",GlobalConst::USER)->active();
    }
    public function scopeMerchantKyc($query) {
        return $query->where("user_type",GlobalConst::MERCHANT)->active();
    }

    public function scopeActive($query) {
        $query->where("status",true);
    }
}
