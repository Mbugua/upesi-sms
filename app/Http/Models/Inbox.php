<?php

namespace App\Models;

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
        'text',
        'to',
        'network',
    ];
}
