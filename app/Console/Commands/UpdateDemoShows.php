<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:update-demo-shows')]
#[Description('Command description')]
class UpdateDemoShows extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $latestShow = \App\Models\Show::orderBy('start_time', 'desc')->first();
        if ($latestShow) {
            $latestStartTime = \Carbon\Carbon::parse($latestShow->start_time);
            $now = now();
            
            if ($latestStartTime->lessThan($now->copy()->addDays(3))) {
                // Tính số ngày cần cộng thêm để lịch chiếu mới nhất dời sang 7 ngày tới
                $daysToAdd = (int) ceil($now->copy()->addDays(7)->diffInDays($latestStartTime->copy()->startOfDay(), false) * -1);
                
                if ($daysToAdd > 0) {
                    \Illuminate\Support\Facades\DB::update(
                        "UPDATE shows SET start_time = DATE_ADD(start_time, INTERVAL ? DAY), end_time = DATE_ADD(end_time, INTERVAL ? DAY)", 
                        [$daysToAdd, $daysToAdd]
                    );
                    \Illuminate\Support\Facades\Cache::flush();
                    $this->info("Shifted all shows forward by {$daysToAdd} days.");
                }
            } else {
                $this->info("Shows are sufficiently in the future. No update needed.");
            }
        }
    }
}
