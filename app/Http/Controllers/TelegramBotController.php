<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function handleWebhook(Request $request)
    {
        $json = $request->getContent();
        $updates = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (isset($updates['message']['message_id']) && isset($updates['message']['text'])) {
            $chatId = $updates['message']['chat']['id'];
            $messageId = $updates['message']['message_id'];
            $messageText = $updates['message']['text'];

            // Check if the message contains the word "تبلیغ"
            if (mb_stripos($messageText, 'تبلیغ') !== false) {
                // Attempt to delete the message
                $this->deleteTelegramMessage($chatId, $messageId);
            }
        }

        return response()->json(['status' => 'success']);
    }


    private function deleteTelegramMessage($chatId, $messageId)
    {
        $token = '6984013434:AAE3dnFuBb1lcs4bm479aErjzSim2gvZc98';

        $url = "https://api.telegram.org/bot{$token}/deleteMessage";

        $response = Http::post($url, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);

        if ($response->successful()) {
            Log::info("Message deleted successfully. Chat ID: {$chatId}, Message ID: {$messageId}");
        } else {
            Log::error("Error deleting message. Chat ID: {$chatId}, Message ID: {$messageId}, Response: {$response->status()} - {$response->body()}");
        }
    }
}
