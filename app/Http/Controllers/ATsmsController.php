<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ATsmsController extends Controller
{
    function sms(Request $request){

        return response()->json([
            'status'=>'Ok',
            'message'=>"Karibu ATsmsController"
        ],200);

    }
}
