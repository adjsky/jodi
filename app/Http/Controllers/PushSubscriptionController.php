<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'fcm_token' => 'required|string',
            'platform' => 'required|string|in:web,android',
            'device_id' => 'required|string',
        ]);

        $this->user()->pushSubscriptions()->updateOrCreate(
            ['device_id' => $data['device_id']],
            $data,
        );

        return back();
    }
}
