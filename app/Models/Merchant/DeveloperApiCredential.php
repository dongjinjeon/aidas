<?php

namespace App\Models\Merchant;

use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeveloperApiCredential extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
