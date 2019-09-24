<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table='report';
    //{"phoneNumber":"+254797561830","failureReason":"DeliveryFailure","retryCount":"0","id":"ATXid_47f18d7e4952b41692e1e3a4e686f28d","status":"Failed","networkCode":"63902"}
    protected $fillable=[
        'reference',
        'phoneNumber',
        'failureReason',
        'deliveryFailure',
        'retryCount',
        'atMessageID',
        'status',
        'networkCode',
        'network'

    ];
}
