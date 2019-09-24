<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Create outbox sms
 */
class Outbox extends Model
{
    protected $table='outbox';
    protected $fillable=[
        'reference',
        'to',
        'message',
        'from',
    ];

}
