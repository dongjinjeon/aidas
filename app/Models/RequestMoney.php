<?php

namespace App\Models;

use App\Constants\GlobalConst;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestMoney extends Model
{
    use HasFactory;
    protected $guarded = ["id"];
    protected $casts = [
        'id' => 'integer',
        'identifier' => 'string',
        'user_id' => 'integer',
        'request_amount' => 'double',
        'request_currency' => 'string',
        'exchange_rate' => 'double',
        'percent_charge' => 'double',
        'fixed_charge' => 'double',
        'total_charge' => 'double', 
        'total_payable' => 'double', 
        'link' => 'string', 
        'remark' => 'string', 
        'status' => 'integer', 
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    public function getStringStatusAttribute() {
        $status = $this->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == GlobalConst::APPROVED) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => "Success",
            ];
        }else if($status == GlobalConst::PENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Pending",
            ];
        }

        return (object) $data;
    }
}
