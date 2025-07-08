<?php

namespace App\Http\Controllers;

use App\Services\HubspotService;
use Illuminate\Http\Request;

class HubspotController extends Controller
{
    public function redirect()
    {
        return redirect()->away(HubspotService::getAuthUrl());
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect()
                ->back()
                ->withErrors(['message' => 'Authorization code not provided']);
        }

        try {
            $tokens = HubspotService::getAccessToken($code);

            $user = $request->user();
            $user->hubspot_access_token = $tokens['access_token'];
            $user->hubspot_refresh_token = $tokens['refresh_token'];
            $user->hubspot_token_expires_at = now()->addSeconds($tokens['expires_in']);
            $user->save();

            return redirect()
                ->route('home')
                ->with('success', 'HubSpot account linked successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('home')
                ->withErrors([
                    'message' => 'Failed to link HubSpot account: ' . $e->getMessage()
                ]);
        }
    }

    public function sync(Request $request)
    {
        $hubspotService = new HubspotService($request->user());
        try{
            $hubspotService->sync();
            return response(200);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
