<?php

namespace App\Jobs;

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
    public function handle(TransactionService $transactionService): void
    {
        //evaluate transactional rules
        $transactionService->evaluateRules($this->userId, $this->amount);
    }
}
