<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::command('aviona:send-schedule-reminders')
    ->everyMinute()
    ->withoutOverlapping(10);

Schedule::command('aviona:send-daily-agenda')
    ->dailyAt('05:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(30);

Schedule::command('aviona:sync-google')
    ->everyFifteenMinutes()
    ->withoutOverlapping(15);

Schedule::call(fn () => DB::table('telegram_link_tokens')
    ->where('expires_at', '<=', now())
    ->delete())
    ->daily();
