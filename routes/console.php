<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::command('aviona:send-schedule-reminders')
    ->everyMinute()
    ->withoutOverlapping(10);

Schedule::call(fn () => DB::table('telegram_link_tokens')
    ->where('expires_at', '<=', now())
    ->delete())
    ->daily();
