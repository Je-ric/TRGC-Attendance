<?php

namespace App\Console\Commands;

use App\Services\AttendanceSummaryService;
use App\Models\Person;
use Illuminate\Console\Command;

class RecomputeAttendanceSummaries extends Command
{
    protected $signature   = 'attendance:recompute-summaries';
    protected $description = 'Recompute attendance_summaries for all people from raw records.';

    public function handle(AttendanceSummaryService $service): int
    {
        $count = Person::count();
        $this->info("Recomputing summaries for {$count} people…");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        Person::pluck('id')->each(function ($id) use ($service, $bar) {
            $service->recompute($id);
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
