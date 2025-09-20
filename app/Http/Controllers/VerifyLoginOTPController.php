<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class VerifyLoginOTPController extends Controller
{
    public function index()
    {
        return inertia('VerifyLoginOTP');
    }
}
