<?php

namespace App\Jobs;
use App\Models\Outbox;
use App\Http\Requests\ATClient;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOutbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $retries;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data=$data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //save sms
        $outbox=Outbox::create($this->data);
        //send sms to AT
        $outboxResponse= ATClient::sendSMS($this->data);
        // if($outboxResponse['status']==='success'){
        //     $outbox->cost=$outboxResponse['Recipients'];
        // }
        var_dump($outboxResponse->SMSMessageData);
        Log::debug('outboxResponse >>'.\json_encode($outboxResponse));
        //commit transaction
        // $outbox->save();

    }
}
