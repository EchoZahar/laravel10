<?php

namespace App\Services\Portal;

use App\Contracts\Portal\PortalFTPContract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PortalDataProcessing implements PortalFTPContract
{

    public function ftpConnection()
    {
        File::deleteDirectory(storage_path('app/public/portal/'));
        $data = Storage::disk('portal_ftp')->files();
        $res = Storage::disk('portal_ftp')->get($data[0]);
        Storage::disk('portal')->put($data[0], $res);

        if (File::exists(storage_path('app/public/portal/' . $data[0]))) {
            $unzip = new ZipArchive();
            if (($unzip->open(storage_path('app/public/portal/' . $data[0])) === true)) {
                File::makeDirectory(storage_path('app/public/portal/unzip'));
                $unzip->extractTo(storage_path('app/public/portal/unzip'));
                $unzip->close();
            } else {
                throw new \Exception('Что то пошло не так !');
            }
        }
    }

    public function rewriteIncomingData()
    {
        // TODO: Распарсить данные.

    }
}
