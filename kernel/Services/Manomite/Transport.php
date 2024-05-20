<?php
namespace Manomite\Services\Manomite;
use \Curl\Curl;

include __DIR__ . '/../../../autoload.php';

class Transport
{
    protected $transport;
    protected $endpoint;
   
    public function __construct()
    {
        $this->endpoint = CONFIG->get('manomite_app_endpoint');
        $this->transport = new Curl();
        $this->transport->setHeader('Request', APP_DOMAIN);
        $this->transport->setHeader('Content-Type', 'application/json');
       
    }

    public function post($endpoint, array $data){
        try {
            return $this->response($this->transport->post($this->endpoint.$endpoint, json_encode($data)));
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function get($endpoint, array $data){
        try {
            return $this->response($this->transport->get($this->endpoint.$endpoint, $data));
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function response($response){
        return json_decode(json_encode($response), true);
    }
}