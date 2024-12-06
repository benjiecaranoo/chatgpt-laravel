<?php

namespace App\Services;

use App\Models\ChatHistory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OpenAIService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.open_ai.token');
        $this->apiUrl = config('services.open_ai.url');
    }

    /**
     * @throws ConnectionException
     */
    public function getChatResponse(string $message, string $model = 'gpt-3.5-turbo'): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $model,
                'messages' => [[
                    'role' => 'user',
                    'content' => $message,
                ]],
                'temperature' => 0,
                'max_tokens' => 2048
            ]);

            if (!$response->successful()) {
                throw new ConnectionException('API request failed: ' . $response->body());
            }

            $body = $response->json();
            $chatResponse = $body['choices'][0]['message']['content'] ?? '';

            if (!empty($chatResponse)) {
                $this->saveChatHistory($message, $chatResponse);
            }

            return $chatResponse;

        } catch (ConnectionException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return "Error: Unable to get chat response";
        }
    }

    public function saveChatHistory(string $message, string $chatResponse): void
    {
        if (Auth::check()) {
            ChatHistory::create([
                'user_id' => Auth::id(),
                'message' => $message,
                'response' => $chatResponse,
            ]);
        }
    }
}
