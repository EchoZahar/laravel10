<?php

namespace App\Console\Commands\Portal;

use App\Contracts\Portal\PortalFTPContract;
use App\Models\PortalNomenclature;
use App\Models\PortalStock;
use App\Services\Portal\CheckedAndWritePortalData;
use Illuminate\Console\Command;

class PortalFtpData extends Command
{
    public function __construct(protected PortalFTPContract $connection)
    {
        parent::__construct();
    }

    protected $signature = 'portal:syncData';

    protected $description = 'Синхронизация данных с порталом...';

    public function handle(CheckedAndWritePortalData $data): void
    {
        $this->output->writeln($this->description . ' Start');
        $this->connection->ftpConnection();
        // todo: записать данные из файлов categories.json
        if (count(glob(storage_path('app/public/portal/unzip/') . 'warehouse.*')) > 0) {
            $startWh = now();
            $bar = $this->output->createProgressBar(count(glob(storage_path('app/public/portal/unzip/') . 'warehouse.*')));
            $bar->start();
            foreach (glob(storage_path('app/public/portal/unzip/') . 'warehouse.*') as $file) {
                $data->checkWarehousesData($file);
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->comment('Check warehouses: ' . $startWh->diff(now())->format('%i min, %s sec'));
        }

        if (count(glob(storage_path('app/public/portal/unzip/') . 'nomenclatures*.*')) > 0) {
            $startWh = now();
            $bar = $this->output->createProgressBar(count(glob(storage_path('app/public/portal/unzip/') . 'nomenclatures*.*')));
            $bar->start();
            PortalNomenclature::query()->truncate();
            foreach (glob(storage_path('app/public/portal/unzip/') . 'nomenclatures*.*') as $file) {
                $data->checkNomenclatureData($file);
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->comment('Check nomenclatures: ' . $startWh->diff(now())->format('%i min, %s sec'));
        }

        if (count(glob(storage_path('app/public/portal/unzip/') . 'stock*.*')) > 0) {
            $startWh = now();
            $bar = $this->output->createProgressBar(count(glob(storage_path('app/public/portal/unzip/') . 'stock*.*')));
            $bar->start();
            PortalStock::query()->truncate();
            foreach (glob(storage_path('app/public/portal/unzip/') . 'stock*.*') as $file) {
                $data->checkStocksData($file);
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->comment('Check stocks: ' . $startWh->diff(now())->format('%i min, %s sec'));
        }

        if (count(glob(storage_path('app/public/portal/unzip/') . 'categories.*')) > 0) {
            $startCat = now();
            $data->checkCategoriesData(glob(storage_path('app/public/portal/unzip/') . 'categories.json'));
            $this->newLine();
            $this->comment('Check categories: ' . $startCat->diff(now())->format('%i min, %s sec'));
        }

        $this->output->writeln($this->description . ' End');
    }
}
