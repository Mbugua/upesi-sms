<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
    use Enums;
    protected $fillable=[
        'reference',
        'to',
        'message',
        'from',
        'status',
        ''

    ];

}
