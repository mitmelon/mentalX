<?php
namespace Manomite\Services\Manomite;
use Manomite\{
    Database\FileDB as DB,
    Engine\DateHelper,
};

require_once __DIR__."/../../../autoload.php";
class Token {
    protected $date;
    public $access;

    public function __construct(string $caller)
    {
        $this->date = new DateHelper;

        $provider = new \Manomite\Oauth\Client(CONFIG->get('manomite_app_default_client'), CONFIG->get('manomite_app_default_secret'), APP_DOMAIN);
        $db = new DB('manomite/oauth/client_credentials');
        if(!$db->has($caller)){
            $accessToken = $provider->client_credentials();
            $db->createDocument($caller, array_merge(array('created_timestamp' => $this->date->nowTimestamp()), $accessToken));
        }
        $token = $db->find($caller);
        if($token['expires'] < $this->date->nowTimestamp()){
            //create new access token
            $accessToken = $provider->client_credentials();
            $db->updateDocument($caller, array_merge(array('updated_timestamp' => $this->date->nowTimestamp()), $accessToken));
        }
        $token = $db->find($caller);
        $this->access = $token['access_token'];
    }
}