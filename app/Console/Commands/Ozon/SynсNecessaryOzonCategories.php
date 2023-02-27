<?php

namespace App\Console\Commands\Ozon;

use App\Contracts\Ozon\SyncOzonCategories;
use Illuminate\Console\Command;

class SynсNecessaryOzonCategories extends Command
{
    public function __construct(protected SyncOzonCategories $sync)
    {
        parent::__construct();
    }

    protected $signature = 'ozon:syncCategories';

    protected $description = 'Sync necessary ozon categories...';

    public function handle(): void
    {
        $start = now();
        $this->output->writeln('Start "' . $this->description . '" at: ' . $start->format('d.m.Y H:i:s'));
        $this->sync->findNecessaryParentOzonCategory();
        $this->output->writeln('End "' . $this->description . '" at: ' . $start->diff(now())->format('%i mim, %s sec'));
    }
}
