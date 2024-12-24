<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipient extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'user_id' => 'integer',
        'country' => 'string',
        'type' => 'string',
        'firstname' => 'string',
        'lastname' => 'string',
        'city' => 'string',
        'state' => 'string',
        'address' => 'string',
        'zip_code' => 'string',
        'details' => 'object',
    ];
    public function scopeAuth($query) {
        $query->where("user_id",auth()->user()->id);
    } 
    public function getFullNameAttribute()
    { 
        return $this->firstname . ' ' . $this->lastname;
    }
    public function user() {
        return $this->belongsTo(User::class);
    } 
    public function receiver() {
        return $this->belongsTo(User::class,'email','email');
    } 
}
