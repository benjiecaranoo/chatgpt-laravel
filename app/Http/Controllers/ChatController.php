<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use App\Services\OpenAIService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected OpenAIService $openAIService;
    public function __construct(OpenAIService $openAIService)
    {
        // $this->middleware('auth');
        $this->openAIService = $openAIService;
    }

    public function index()
    {
        $chatHistory = ChatHistory::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json(['history' => $chatHistory]);
    }

    /**
     * @throws ConnectionException
     */
    public function store(Request $request)
    {
        $response = $this->openAIService->getChatResponse($request->input("message"));

        return response()->json(['response' => $response]);
    }
}
