<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;
    protected $user;

    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function handle()
    {
        try {
            // Process payment logic here
            $this->payment->update(['status' => 'processing']);
            
            // Simulate payment processing
            sleep(2);
            
            $this->payment->update([
                'status' => 'completed',
                'processed_at' => now()
            ]);

            // Update user's water units and balance
            $this->user->update([
                'water_units' => 0,
                'balance' => 0
            ]);

            Log::info('Payment processed successfully', [
                'payment_id' => $this->payment->id,
                'user_id' => $this->user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage()
            ]);
            
            $this->payment->update(['status' => 'failed']);
            throw $e;
        }
    }
} 