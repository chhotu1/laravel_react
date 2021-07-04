<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public $errors = [];
    public $extraData = [];
    public $data = [];
    public $message = "";
    public $status = true;
    public $errorCode = "";

    protected function sendResult() 
    {
        if(isset($this->errorCode) && $this->errorCode != "") 
        {

            $errorCode = $this->errorCode;
        } else 
        {

            $errorCode = $this->status ? 200 : 422;
        }
        
        $result = [
            "message" => $this->message,
            "extraData" => $this->extraData,
            "status" => $this->status,
            "data" => $this->data,
            "errors" => $this->errors
        ];

        return response()->json($result,$errorCode);

    }
}
