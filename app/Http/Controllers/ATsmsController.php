<?php

namespace App\Http\Controllers;
use Validator;
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
     * Send sms
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

        //To do
        //send to que
        //get receipt
        //update original message in DB

        $response = ATClient::sendSMS($data);
        return response()->json($response);

    }
    /**
     * Fetch inbox messages in application
     */
    function messages(Request $request){
        Log::info('fetching messages');
        $lastReceivedId=$request->input('lastReceivedId');
        //'ATXid_a99c7a6504002d09a873e6399c97340b';
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
        return response()->json([$request->all()],
        200);
    }
    /**
     * Delivery reports
     */
    function notify(Request $request){
        Log::debug('check delivery status'.\json_encode($request->all()));
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
