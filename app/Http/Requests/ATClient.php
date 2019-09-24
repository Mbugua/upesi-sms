<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;

class ATClient
{
    protected $username;
    protected $apiKey;
    protected $AT;

    static function getATClient(){
        $username = env('AT_USERNAME');
        $apiKey = env('AT_API_KEY');
        $response=[];
        try{
            Log::info('[ATClient::getATClient] >> connecting to AT and get new instance');
            $AT =(new AfricasTalking($username,$apiKey));
            ($AT) ? ($AT): null ;
            return $AT;
        }catch(Exception $e){
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
            Log::error('[ATClient::getATClient] >> Exception' .json_encode($responseBodyAsString));
        }
    }

    static function sendSMS(array $data=[]){
        Log::info('[ATClient::sendSMS] prepare sms payload to send to AT gateway');
        try{
            Log::info('[ATClient::sendSMS] sms payload '.json_encode($data));
            $AT=self::getATClient();
            ($AT)? $response=  $AT->sms()->send($data) :
            $response=['error'=>'406','message'=>'Could Not Send SMS to AT'];
            Log::info('[ATClient::sendSMS] >> response' .json_encode($response));

        }catch(Excetpion $e){
           $response=['error'=>$e->getCode(), 'message'=>$e->getMessage()];
            Log::error('Exception' .json_encode($response));
        }
        return $response;
    }

    static function fetchMessages($data){

        Log::info('[ATClient::fetchMessages] >> fetch messages');
        try{
            Log::info('[ATClient::fetchMessages] > fetch message payload '.json_encode($data));
            // TO DO
            // loop here to get messages
            $AT=self::getATClient();
            ($AT) ? $response= ($AT->sms()->fetchMessages([$data]) ):
            $response=['error'=>'407','message'=>'Could Not Fetch Inbox Messages from AT'];
            Log::info('[ATClient::fetchMessages] >> response ' .\json_encode($response));
        }catch(Excetpion $e){
            $response=['error'=>$e->getCode(), 'message'=>$e->getMessage()];
            Log::debug('Exception' .json_encode($response));

        }
    }
}