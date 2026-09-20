<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReleaseSeatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $order = Order::where('id', $this->orderId)->lockForUpdate()->first();
            
            if ($order && $order->status === 'pending') {
                $order->update(['status' => 'cancelled']);
            }
        });
    }
}
