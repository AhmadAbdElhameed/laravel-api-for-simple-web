<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewMessageRequest;
use App\Models\Message;
use Illuminate\Http\Request;

class MesaageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(NewMessageRequest $request)
    {
//        $data = $request->validated();
//        $record = Message::create($data);
//        if ($record) {
//            return ApiResponse::sendResponse(201, 'Sent Successfully', []);
//        }


        $data = $request->validated();
        $message = Message::create($data);
        if($message) {
            return ApiResponse::sendResponse(201,'Message Sent Successfully',[]);
        }

    }
}