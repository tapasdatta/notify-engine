<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})
    ->purpose("Display an inspiring quote")
    ->everyMinute();

//check for inactivity login last login
// run a code which checks whether users have setup 'not logged in rule to send email'.
// if yes queue their notification will be processed later time
// notification cool down period

// Schedule::call(function () {})->daily();

//check for transaction_threasold

//check for transaction_count

//check fo
