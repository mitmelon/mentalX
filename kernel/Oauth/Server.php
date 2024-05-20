<?php
ini_set('display_errors',1);error_reporting(E_ALL);
include __DIR__ . '/../../autoload.php';

$username = DB_USER;
$password = DB_PASS;
$host = DB_HOST;
$port = DB_PORT;
$client_id = MONGO_CLIENT_ID;
$client_secret = MONGO_CLIENT_SECRET;


$mongo = new \MongoClient('mongodb://'.$username.':'.$password.'@'.$host.':'.$port);
$storage = new \OAuth2\Storage\Mongo($mongo);
$storage->setClientDetails($client_id, $client_secret, null, null, 'admin', APP_DEPLOYER_ID);

$config = array(
    'access_lifetime' => 86400,
    'use_jwt_access_tokens'        => false,
    'store_encrypted_token_string' => true,
    'use_openid_connect'       => false,
    'id_lifetime'              =>  86400,
    'www_realm'                => 'Service',
    'token_param_name'         => 'access_token',
    'token_bearer_header_name' => 'Bearer',
    'enforce_state'            => true,
    'require_exact_redirect_uri' => true,
    'allow_implicit'           => false,
    'allow_credentials_in_request_body' => true,
    'allow_public_clients'     => true,
    'always_issue_new_refresh_token' => true,
    'refresh_token_lifetime' => 157248000,
    'unset_refresh_token_after_use' => true,
);

$server = new \OAuth2\Server($storage, $config);
$server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
$server->addGrantType(new \OAuth2\GrantType\RefreshToken($storage));