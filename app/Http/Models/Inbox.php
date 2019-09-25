<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Handle incoming messages from AT
 */
class Inbox extends Model
{
    protected $table='inbox';
    protected $fillable=[
        'date',
        'from',
        'messageid',
        'linkid',
        'message',
        'to',
        'networkcode',
        'network',
    ];
}
