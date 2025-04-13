<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller 
{
    use AuthorizesRequests, ValidatesRequests;


    protected function respond($data, $status = 200, array $headers = [])
    {
        return response()->json($data, $status, $headers);
    }

   
    protected function respondSuccess($data = null, $message = null, $status = 200)
    {
        return $this->respond([
            'success' => true,
            'data' => $data,
            'message' => $message ?? 'Opération réussie',
        ], $status);
    }

   
    protected function respondError($message, $errors = [], $status = 400)
    {
        return $this->respond([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    protected function authorizeRH()
    {
        if (!auth()->user()->isRH()) {
            abort(403, 'Action réservée au service RH');
        }
    }
}