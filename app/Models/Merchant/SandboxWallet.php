<?php

namespace App\Models\Merchant;

use App\Constants\GlobalConst;
use App\Models\Admin\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SandboxWallet extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function scopeAuth($query) {
        return $query->where('merchant_id',auth()->user()->id);
    }

    public function scopeActive($query) {
        return $query->where("status",true);
    } 

    public function merchant() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function scopeSender($query) {
        return $query->whereHas('currency',function($q) {
            $q->where("sender",GlobalConst::ACTIVE);
        });
    }
}
