<?php

namespace App\Http\Controllers;

use App\Services\GmailService;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function sync(Request $request)
    {
        $gmailService = new GmailService($request->user());
        try {
            $gmailService->syncEmails();
            return response(200);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
