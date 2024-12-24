<?php

namespace App\Models\Merchant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantAuthorization extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    
    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
