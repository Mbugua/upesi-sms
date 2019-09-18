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
    function sms(Request $request){
        //validate input
        $validator= Validator::make($request->all(),[
            'recipient'=>'required',
            'message'=>'required|max:160'
        ])->validate();

        $hash=new Hashids('upesi-sms-at-api');
        $recipient=$request->input('recipient');
        $message=$request->input('message');
        $from=env('AT_SHORTCODE');
        $reference=$hash->encode(time(),intval($message,env('AT_SHORTCODE')));
        $enque=env('AT_ENQUEUE');
        $data=[
            'to'=>$recipient,
            'message'=>$message,
            'from'=>$from,
            'reference'=>$reference,
            'enqueue'=>$enque
        ];
        Log::debug('some shit here'.json_encode($data));
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
        return response()->json([$request->all()]
        ,200);
    }
}
