<?php

namespace App\Console\Commands\Aliexpress;

use App\Contracts\Aliexpress\SyncAliexpressCategories;
use Illuminate\Console\Command;

class SynсNecessaryAliexpressCategories extends Command
{
    public function __construct(protected SyncAliexpressCategories $syncCategories)
    {
        parent::__construct();
    }

    protected $signature = 'aliexpress:syncCategories';

    protected $description = 'Sync necessary aliexpress categories...';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $start = now();
        $this->info('Start sync: ' . $start->format('d.m.Y H:i:s') . ' ' . $this->description);
        $this->syncCategories->getParentsCategoriesAndSynchronization();
        $this->output->writeln('End time sync: ' . $start->diff(now())->format('%i mim, %s sec') . ' ' . $this->description);
    }
}
