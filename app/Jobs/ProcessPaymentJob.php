<?php

namespace App\Jobs;

use App\Http\Services\ProcessBookPaymentService;
use App\Http\Services\ProcessCardsPaymentService;
use App\Http\Services\ProcessServicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $paymentType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($paymentType, $data)
    {
        $this->paymentType = $paymentType;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ProcessCardsPaymentService $processCardsPaymentService,
        ProcessServicePayment $processServicePayment,
        ProcessBookPaymentService $processBookPaymentService
    ) {
        try {
            switch ($this->paymentType) {
                case 'cards':
                    $processCardsPaymentService->processCardsPayment($this->data);
                    break;
                case 'book':
                    $processBookPaymentService->processBookPayment($this->data);
                    break;
                case 'service':
                    $processServicePayment->processServicePayment($this->data);
                    break;
                default:
                    Log::warning('Unknown payment type in ProcessPaymentJob', ['type' => $this->paymentType]);
                    break;
            }
        } catch (\Throwable $e) {
            Log::error('Payment processing failed in Job', [
                'error' => $e->getMessage(),
                'type' => $this->paymentType,
                'data' => $this->data
            ]);
            // Optionally release the job back to the queue to retry
            // $this->release(10);
            throw $e;
        }
    }
}
