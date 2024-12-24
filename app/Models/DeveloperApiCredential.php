<?php

namespace App\Models;

use App\Models\User; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeveloperApiCredential extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'id' => 'integer', 
        'user_id' => 'integer',
        'client_id' => 'string', 
        'client_secret' => 'string', 
        'status' => 'boolean', 
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];
    public function merchant() {
        return $this->belongsTo(User::class,'user_id');
    }
}
