<?php
namespace Manomite\Services\Manomite;
use \Manomite\Engine\Platform\Health;

require_once __DIR__."/../../../autoload.php";
class Client extends Transport {
    private $platform;

    public function create_platform_profile(){
        $config = json_decode(file_get_contents(SYSTEM_DIR.'/config/app.json'), true);
        if(is_array($config)){
            $this->platform();
            $response = $this->register();
            if($response['status'] === 200){
                $config['app_deployer_id'] = $this->platform['id'];
                $config['public_address'] = $response['address'];
                //the public address is used to encrypt your data communications with Manomite
                file_put_contents(SYSTEM_DIR.'/config/app.json', json_encode($config));
            }
        }
    }

    private function platform():void {
        $platform = new Health(TIMEZONE);
        $computer = $platform->toArray();
        $payload = array(
            'os' => $computer['OS'],
            'ip' => $computer['AccessedIP'],
            'hostname' => $computer['HostName'],
            'cpu' => $computer['CPU'][0],
            'architecture' => $computer['CPUArchitecture'],
            'php_version' => $computer['phpVersion'],
            'web_services' => $computer['webService'],
        );
        $id = hash('sha512', json_encode($payload));
        $this->platform = array('id' => $id, 'payload' => $payload);
    }

    private function register(){
        $this->transport->setHeader('Auth', $this->platform['id']);
        return $this->post('register', $this->platform['payload']);
    }
}