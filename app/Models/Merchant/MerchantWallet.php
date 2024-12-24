<?php

namespace App\Models\Merchant;
 
use App\Models\Admin\Currency;
use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerchantWallet extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    protected $casts = [
        'id'                    => 'integer',
        'merchant_id'               => 'integer',
        'currency_id'           => 'integer',
        'balance'               => 'decimal:8',
        'profit_balance'        => 'decimal:8',
        'status'                => 'boolean',
    ];

    public function scopeAuth($query) {
        return $query->where('merchant_id',auth()->user()->id);
    }

    public function scopeActive($query) {
        return $query->where("status",true);
    }

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }
}
