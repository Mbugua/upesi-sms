<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessOutbox;
use App\Jobs\ProcessNotification;
use App\Jobs\ProcessInbox;
use App\Jobs\ProcessBlacklist;

use Illuminate\Http\Request;
use Hashids\Hashids;
use App\Http\Requests\ATClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class ATsmsController extends Controller
{

    protected $lastReceivedId = 0;
    protected $reference;

    /**
     * Send text message to AT Gateway.
     * @param $to
     * @param $message
     * @param $from :AT_shortcode
     */
    function outbox(Request $request){

        $hash=new Hashids('upesi-sms-at-api');
        $recipient=$request->input('recipient');
        $message=$request->input('message');
        $from=env('AT_SHORTCODE',$request->input('from'));
        $this->reference='obx_'.$hash->encode(time(),intval($message,env('AT_SHORTCODE')));
        Log::debug('reference >'.$this->reference);
        $enque=env('AT_ENQUEUE');
        $data=[
            'to'=>$recipient,
            'message'=>$message,
            'from'=>$from,
            'reference'=>$this->reference,
            'enqueue'=>$enque
        ];
        //send to queue
        ProcessOutbox::dispatch($data)->onQueue('outbound_sms')->delay(3);
        //to do check final response

        //generic response
        return response()->json([
            'response'=>['status'=>'success','data'=>[
                'message'=>'ok',
            ]]],200);

    }
    /**
     *  Receive incoming messages
     */
    function incoming(Request $request){
        $data=$request->all();
        Log::info('incoming sms >>>'.\json_encode($data));
           if (!$data){
                return \response()->json(['response'=>['status'=>'failed',
                'data'=>[
                    'error'=>400,
                    'message'=>'Unknown Request'
                ]]],400);
           }
           //send incoming sms to queue
           $inbox=[
               'to'=>$data['to'],
               'from'=>$data['from'],
               'linkid'=>$data['linkId'],
               'network'=>'KENYA.SAFARICOM',
                'text'=>$data['text'],
                'messageid'=>$data['id'],
                'date'=>$data['date'],
                ];
           ProcessInbox::dispatch($inbox)->onQueue('incoming_sms')->delay(3);
        //return generic response
        return \response()->json([
            'response'=>[
                'status'=>'success',
                'data'=>[
                    'message'=>'ok',
                    ]]],200);

    }
    /**
     * Delivery reports
     */
    function notify(Request $request){
        $data=$request->all();
        Log::info('delivery report'.\json_encode($data));
        //return a 400 incase requests timout or AT craps  itself
        if(!$data){
                    return response()->json([
                        'response'=>[
                            'status'=>'failed',
                            'data'=>[
                                'message'=>'Unknown Request',
                                'error'=>406
                            ]]], 406);
        }
        if($data){
            $notifyData=[
                'phoneNumber'=>isset($data['phoneNumber'])?$data['phoneNumber']:null,
                'failureReason'=>isset($data['failureReason'])?$data['failureReason']:null,
                'retryCount'=>isset($data['retryCount'])?$data['retryCount']:null,
                'messageID'=>isset($data['id'])?$data['id']:null,
                'status'=>isset($data['status'])?$data['status']:null,
                'networkCode'=>isset($data['networkCode'])?$data['networkCode']:null,
                'network'=>'KENYA.SAFARICOM'
            ];
            ProcessNotification::dispatch($notifyData)->onQueue('delivery_reports');
        }
        return \response()->json([
            'response'=>[
                'status'=>'success',
                'data'=>[
                    'message'=>'ok',
                    ]]],200);

    }

    /**
     * Return a 404 message incase of a spam request
     * @param $request
     */
    function notFound(Request $request){
        Log::info('<<<< invalid request >>>>>'.\json_encode($request->all()));
        return \response()->json([
            'response'=>['status'=>'failed',
                'data'=>[
                    'message'=>"Not Found",
                    'error'=>404
                ]
            ]
         ],404);
    }

    /**
     * Blacklist
     * @param $senderid
     * @param $phonenumber
     */
    function blacklist(Request $request){
         Log::info('blacklist >'.\json_encode($request->all()));
         $data=$request->all();
         if(!$data){
        return \response()->json([
                'response'=>['status'=>'failed',
                    'data'=>[
                        'message'=>"Bad Request",
                        'error'=>400
                    ]
                ]
            ],404);
         }
         ProcessBlacklist::dispatch($data)->onQueue('blacklist')->delay(5);

        return   \response()->json([ 'response'=>['status'=>'failed',
                    'data'=>[
                        'message'=>"Ok",
                    ]
                ]
            ],200);

    }
}
