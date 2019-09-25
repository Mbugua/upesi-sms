<?php

namespace App\Models;
GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
class Notification extends Model
{
    protected $table='notification';
    protected $uuidVersion=1;
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

        /**
     * @throws \Exception
     * @return string
     */
    protected function generateUuid(): string
    {
        // UUIDv3 has been used here, but you can also use UUIDv5.
        return Uuid::uuid3(Uuid::NAMESPACE_DNS, 'upesi-sms')->toString();
    }
}
