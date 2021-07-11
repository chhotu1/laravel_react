<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public $errors = [];
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
            "status" => $this->status,
            "data" => $this->data,
            "errors" => $this->errors
        ];

        return response()->json($result);

    }
}
