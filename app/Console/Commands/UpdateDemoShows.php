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
        $now = now()->startOfDay();
        $endOfWeek = now()->addDays(7)->endOfDay();
        
        $datesWithShows = \App\Models\Show::whereBetween('start_time', [$now, $endOfWeek])
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(start_time) as date'))
            ->distinct()
            ->pluck('date');
            
        // Nếu trong 7 ngày tới chưa phủ kín lịch chiếu (có ngày bị trống)
        if ($datesWithShows->count() < 7) {
            $showingMovies = \App\Models\Movie::where('status', 'showing')->get();
            $rooms = \App\Models\Room::all();
            $times = ['08:00', '09:30', '11:00', '13:00', '14:30', '16:00', '18:30', '20:00', '21:30'];
            $prices = [75000, 85000, 95000, 100000, 120000];
            
            $existingDates = $datesWithShows->toArray();
            
            for ($day = 0; $day < 7; $day++) {
                $dateStr = now()->addDays($day)->format('Y-m-d');
                
                // Bỏ qua nếu ngày này đã có lịch chiếu để tránh trùng lặp
                if (in_array($dateStr, $existingDates)) {
                    continue;
                }
                
                // Lấy tất cả các slot trống trong ngày và trộn ngẫu nhiên
                $availableSlots = [];
                foreach ($rooms as $room) {
                    foreach ($times as $time) {
                        $availableSlots[] = ['room' => $room, 'time' => $time];
                    }
                }
                shuffle($availableSlots);
                $slotIndex = 0;
                
                // Xáo trộn phim để lịch chiếu không bị nhàm chán
                $shuffledMovies = $showingMovies->shuffle();
                
                foreach ($shuffledMovies as $movie) {
                    // Mỗi phim có ngẫu nhiên 1 đến 4 suất chiếu mỗi ngày tùy theo slot trống
                    $showsPerDay = rand(1, 4);
                    
                    for ($i = 0; $i < $showsPerDay; $i++) {
                        if ($slotIndex >= count($availableSlots)) break 2;
                        
                        $slot = $availableSlots[$slotIndex];
                        $startTime = \Carbon\Carbon::parse("{$dateStr} {$slot['time']}");
                        $endTime = $startTime->copy()->addMinutes($movie->duration_minutes + 20);
                        
                        \App\Models\Show::create([
                            'movie_id' => $movie->id,
                            'room_id' => $slot['room']->id,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'price' => $prices[array_rand($prices)],
                        ]);
                        
                        $slotIndex++;
                    }
                }
            }
            \Illuminate\Support\Facades\Cache::flush();
            $this->info("Đã tạo thêm các suất chiếu đa dạng cho các ngày bị thiếu trong 7 ngày tới.");
        } else {
            $this->info("Đã có đủ lịch chiếu đa dạng cho cả tuần, không cần cập nhật.");
        }
    }
}
