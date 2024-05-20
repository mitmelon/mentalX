<?php
namespace Manomite\Engine\Security\Antivirus;
use Socket\Raw\Factory as SocketFactory;
use Xenolope\Quahog\Client as ClamAVScanner;
class Clam
{
    public $clam;
    public function __construct()
    {
        // Create a new socket instance
        $socket = (new SocketFactory())->createClient('tcp://127.0.0.1:3310');
        $this->clam = new ClamAVScanner($socket);
    }
}