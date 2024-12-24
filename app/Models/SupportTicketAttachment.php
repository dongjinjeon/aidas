<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketAttachment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'attachment_info'       => 'object',
    ];
}
