<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessSMS;
use Illuminate\Http\Request;
use Hashids\Hashids;
use App\Http\Requests\ATClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class ATsmsController extends Controller
{

    protected $lastReceivedId = 0;
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
        $reference=$hash->encode(time(),intval($message,env('AT_SHORTCODE')));
        $enque=env('AT_ENQUEUE');
        $data=[
            'to'=>$recipient,
            'message'=>$message,
            'from'=>$from,
            'reference'=>$reference,
            'enqueue'=>$enque
        ];
        //send to que
       $response= ProcessSMS::dispatch($data)->onQueue('outbound_sms')->delay(3);

        return response()->json($response);

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
        Log::debug('check inbox at AT >> '.\json_encode($request->all()));
        //pass requst data to queue
        return response()->json([$request->all()],
        200);
    }
    /**
     * Delivery reports
     */
    function notify(Request $request){
        Log::debug('check delivery status >> '.\json_encode($request->all()));
        return response()->json([$request->all()]
        ,200);
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
