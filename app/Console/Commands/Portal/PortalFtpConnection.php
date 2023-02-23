<?php

namespace App\Console\Commands\Portal;

use Illuminate\Console\Command;

class PortalFtpConnection extends Command
{
    protected $signature = 'portal:syncData';

    protected $description = 'Синхронизация данных с порталом.';

    public function handle(): void
    {
        $this->output->writeln($this->description);
    }
}
