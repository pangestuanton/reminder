<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\TelegramAccountLinked;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $expectedSecret = (string) config('services.telegram.webhook_secret');
        $providedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');

        abort_unless(
            $expectedSecret !== '' && hash_equals($expectedSecret, $providedSecret),
            403,
        );

        if ($request->input('message.chat.type') !== 'private') {
            return response()->noContent();
        }

        $text = trim((string) $request->input('message.text'));

        if (! preg_match('/^\/start(?:@[A-Za-z0-9_]+)?\s+([A-Za-z0-9_-]{1,64})$/', $text, $matches)) {
            return response()->noContent();
        }

        $chatId = $request->input('message.chat.id');

        if (! is_int($chatId) && ! is_string($chatId)) {
            return response()->noContent();
        }

        $user = DB::transaction(function () use ($matches, $chatId): ?User {
            $link = DB::table('telegram_link_tokens')
                ->where('token_hash', hash('sha256', $matches[1]))
                ->lockForUpdate()
                ->first();

            if (! $link || $link->used_at !== null || now()->greaterThan($link->expires_at)) {
                return null;
            }

            $chatId = (string) $chatId;

            $usedByAnotherAccount = User::query()
                ->where('telegram_chat_id', $chatId)
                ->whereKeyNot($link->user_id)
                ->exists();

            if ($usedByAnotherAccount) {
                return null;
            }

            $user = User::query()->find($link->user_id);

            if (! $user) {
                return null;
            }

            $user->forceFill([
                'telegram_chat_id' => $chatId,
                'telegram_linked_at' => now(),
            ])->save();

            DB::table('telegram_link_tokens')
                ->where('token_hash', $link->token_hash)
                ->update(['used_at' => now(), 'updated_at' => now()]);

            return $user;
        });

        if ($user) {
            try {
                Notification::route('telegram', (string) $chatId)
                    ->notify(new TelegramAccountLinked);
            } catch (Throwable) {
                Log::warning('Konfirmasi penautan Telegram gagal dikirim.', [
                    'user_id' => $user->id,
                ]);
            }
        }

        return response()->noContent();
    }
}
