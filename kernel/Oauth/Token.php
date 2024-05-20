<?php
include_once __DIR__.'/Server.php';
$tokenData = $server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->getResponseBody();
exit($tokenData);