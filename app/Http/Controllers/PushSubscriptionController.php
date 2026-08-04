<?php

// app/Http/Controllers/PushSubscriptionController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Subscribe or update push notification endpoint
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        $user = $request->user(); // Authenticated via JWT
        
        $user->updatePushSubscription(
            $request->endpoint,
            $request->input('keys.p256dh'),
            $request->input('keys.auth')
        );

        return response()->json(['message' => 'Subscription saved successfully.']);
    }

    /**
     * Unsubscribe user device
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        $request->user()->deletePushSubscription($request->endpoint);

        return response()->json(['message' => 'Subscription removed successfully.']);
    }
}