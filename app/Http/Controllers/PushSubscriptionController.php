<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string',
            'key' => 'nullable|string',
            'token' => 'nullable|string',
            'contentEncoding' => 'nullable|string',
        ]);

        $this->user()->updatePushSubscription(
            $data['endpoint'],
            $data['key'] ?? null,
            $data['token'] ?? null,
            $data['contentEncoding'] ?? null
        );

        return response(status: 204);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(['endpoint' => 'required|string']);

        $this->user()->deletePushSubscription($data['endpoint']);

        return response(status: 204);
    }
}
