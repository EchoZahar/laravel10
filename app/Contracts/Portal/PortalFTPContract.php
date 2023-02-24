<?php

namespace App\Contracts\Portal;

interface PortalFTPContract
{
    public function ftpConnection();
    public function rewriteIncomingData();
}
