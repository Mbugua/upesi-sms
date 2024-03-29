<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Create outbox sms
 */
class Outbox extends Model
{
    protected $table='outbox';
    protected $retires='1';

    protected $fillable=[
        'reference',
        'to',
        'message',
        'from',
        'cost',
        'retries',
    ];

}
