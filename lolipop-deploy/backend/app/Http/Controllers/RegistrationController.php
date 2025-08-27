<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class RegistrationController extends Controller
{
    /**
     * 登録数を取得
     */
    public function index(): JsonResponse
    {
        $count = User::count();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
