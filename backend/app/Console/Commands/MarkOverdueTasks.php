<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class MarkOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark tasks as overdue if their due date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $overdueTasks = Task::markOverdue()->update(['status' => 'overdue']);
        $this->info("Marked {$overdueTasks} tasks as overdue.");
    }
}
