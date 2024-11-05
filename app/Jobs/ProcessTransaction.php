<?php

namespace App\Jobs;

use App\Services\LogService;
use App\Servies\TransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTransaction implements ShouldQueue
{
    use Queueable;

    public $userId;
    public $amount;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $amount)
    {
        $this->userId = $userId;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     */
    public function handle(
        LogService $logService,
        TransactionService $transactionService
    ): void {
        //log the transaction
        $logService->logTransaction($this->userId, $this->amount);

        //evaluate transactional rules
        $transactionService->evaluateRules($this->userId, $this->amount);
    }
}
