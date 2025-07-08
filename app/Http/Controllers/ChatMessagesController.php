<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatMessageResource;
use App\Services\AiAgentService;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    public function index(Request $request)
    {
        $messages = $request->user()->chatMessages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return ChatMessageResource::collection($messages);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'thread_id' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        $data['is_assistant'] = false;
        $message = $request->user()->chatMessages()->create($data);

        $aiAsistant = new AiAgentService($request->user());

        $messageResponse = $aiAsistant->processMessage($message);

        return new ChatMessageResource($messageResponse);
    }
}
