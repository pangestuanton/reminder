<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('aviona:send-schedule-reminders')->hourly();
