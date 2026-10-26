<?php

namespace App\Console\Commands;

use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BulkShiftOverride extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shiftoverride:bulk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply shift Override in bulk';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        try {
            $this->info('Command started at: ' . now());
            $workers = Worker::where('employment_status', 'Active')->get();
            dd(count($workers));
        } catch (\Exception $e) {
            Log::error('Error during bulk shift override: ' . $e->getMessage());
            $this->error('🔥 Error: ' . $e->getMessage());
        } finally {
            $this->info('Command completed at: ' . now());
        }
    }
}
