<?php
namespace Manomite\Engine\Security;
use \HtaccessFirewall\Host\IP;
use \HtaccessFirewall\HtaccessFirewall;

class Firewall
{

    public function block($ip, $htaccessPath)
    {
        $firewall = new HtaccessFirewall($htaccessPath);
        $host = IP::fromString($ip);
        $firewall->deny($host);
    }

    public function unblock($ip, $htaccessPath)
    {
        $firewall = new HtaccessFirewall($htaccessPath);
        $host = IP::fromString($ip);
        $firewall->undeny($host);
    }
}