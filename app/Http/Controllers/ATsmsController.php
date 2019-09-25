<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessSMS;
use App\Jobs\ProcessNotification;

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
    function send(Request $request){

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
        ProcessSMS::dispatch($data)->onQueue('outbound_sms')->delay(3);
        //to do check final response

        //generic response
        return response()->json([
            'response'=>['status'=>'success','data'=>[
                'message'=>'SMS sent sucessfully',
                'statusCode'=>101,
            ]]],200);

    }
    /**
     * Fetch inbox messages in application
     * prefer to use /api/incoming
     * @params $request
     */
    function messages(Request $request){
        Log::info('fetching messages');
        $lastReceivedId=$request->input('lastReceivedId');
        $data=[
            'username'=>env('AT_USERNAME'),
            'lastReceivedId'=>$lastReceivedId
        ];
        $response =ATClient::fetchMessages($data);
         return response()->json($response);
    }

    /**
     *  Receive incoming messages
     */
    function incoming(Request $request){
        //{"phoneNumber":"+254797561830","failureReason":"DeliveryFailure","retryCount":"0","id":"ATXid_47f18d7e4952b41692e1e3a4e686f28d","status":"Failed","networkCode":"63902"}

        Log::debug('check inbox at AT >> '.\json_encode($request->all()));
        $data=$request->all();
        if($data['statuss'] !='success'){
                    return response()->json([
                        'response'=>[
                            'status'=>'failed',
                            'data'=>[
                                'message'=>'Bad Request from AT endpoint',
                            ]
                        ]
                    ],
        400);
        }
        //pass requst data to queue

    }
    /**
     * Delivery reports
     */
    function notify(Request $request){
        Log::debug('check delivery status >> '.\json_encode($request->all()));
        $data=$request->all();
        //return a 400 incase requests timout or AT craps  itself
        if(!$data){
                    return response()->json([
                        'response'=>[
                            'status'=>'failed',
                            'data'=>[
                                'message'=>'Bad Request from AT endpoint',
                                'error'=>400
                            ]]], 400);
        }
        Log::debug('SMSMessageData >> '.\json_encode($data));
        if($data){
            $notifyData=[
                'outbox_reference'=>$this->reference,
                'phoneNumber'=>isset($data['phoneNumber'])?$data['phoneNumber']:null,
                'failureReason'=>isset($data['failureReason'])?$data['failureReason']:null,
                'retryCount'=>isset($data['retryCount'])?$data['retryCount']:null,
                'messageID'=>isset($data['id'])?$data['id']:null,
                'status'=>isset($data['status'])?$data['status']:null,
                'networkCode'=>isset($data['networkCode'])?$data['networkCode']:null,
                'network'=>'KENYA.SAFARICOM'
            ];
            //process dlr reports
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
        return \response()->json([
            'response'=>[
                'data'=>[
                    'message'=>"Not Found",
                    'error'=>404
                ]
            ]
         ],404);
    }
}
