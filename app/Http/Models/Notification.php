<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
class Notification extends Model
{
    protected $table='notification';

    //{"phoneNumber":"+254797561830","failureReason":"DeliveryFailure","retryCount":"0","id":"ATXid_47f18d7e4952b41692e1e3a4e686f28d","status":"Failed","networkCode":"63902"}
    protected $fillable=[
        'outbox_reference',
        'phoneNumber',
        'failureReason',
        'retryCount',
        'messageID',
        'status',
        'networkCode',
        'network'

    ];



}
