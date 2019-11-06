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
        Log::info("Oubox {".\json_encode($outbox).'}');
        //send sms to AT
        $outboxResponse= ATClient::sendSMS($this->data);
        Log::info("OuboxResponse {".\json_encode($outboxResponse).'}');
        if ($outboxResponse['status']==="false"){
            return;
        }
        elseif(($outboxResponse['status']==='success')  && (null != $outboxResponse['data']->SMSMessageData->Recipients)){
            $outbox->cost=$outboxResponse['data']->SMSMessageData->Recipients['0']->cost;
            $outbox->messageID=$outboxResponse['data']->SMSMessageData->Recipients['0']->messageId;
            $outbox->status=$outboxResponse['data']->SMSMessageData->Recipients['0']->status;
        }
        //commit transaction
        $outbox->save();

    }
}
