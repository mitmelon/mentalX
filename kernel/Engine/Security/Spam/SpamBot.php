<?php
namespace Manomite\Engine\Security\Spam;

use \Manomite\Engine\Bayes\Classifier;
use \Manomite\Engine\Bayes\Tokenizer\WhitespaceAndPunctuationTokenizer;
use \Curl\Curl;

require_once __DIR__."/../../../../autoload.php";

class SpamBot
{
    public function __construct($model = null)
    {
        $this->model = $model;
        if($model === null){
            //auto select latest model
            $files = scandir(__DIR__.'/model', SCANDIR_SORT_DESCENDING);
            $this->model = __DIR__.'/model/'.$files[0];
        }
        $tokenizer = new WhitespaceAndPunctuationTokenizer();
        $this->classifier = new Classifier($tokenizer);
        $this->endpoint = 'https://spamcheck.postmarkapp.com/filter';
        $this->transport = new Curl();
        $this->transport->setHeader('Content-Type', 'application/json');
        $this->transport->setHeader('Accept', 'application/json');
    }

    public function trainBot($label, $text, $autogenemodel = 'on'):void
    {
        $csvFile = fopen(__DIR__.'/data/training.csv', "a");
        fputcsv($csvFile, array($label, $text));
        if ($autogenemodel === 'on') {
            $this->generateTrainedModel();
        }
    }

    public function predict($text):array
    {
        return $this->classifier->classifyFromModel($text, $this->model);
    }

    private function generateTrainedModel():void
    {
        //Training model
        $csvFile = fopen(__DIR__.'/data/training.csv', 'r');
        while (($line = fgetcsv($csvFile)) !== false) {
            $this->classifier->train($line[0], $line[1]);
        }
        $this->classifier->generateModel(__DIR__.'/model', 'spamModel-v2-'.date('d-m-Y'));
    }

    private function cleanResponse($response){
        return json_decode(json_encode($response), true);
    }

    public function spamScore($message){
        $this->transport->post($this->endpoint, [
            'email' => $message,
            'options' => 'short',
         ]);
        if ($this->transport->error) {
            return $this->transport->errorMessage;
         } else {
             return $this->cleanResponse($this->transport->response);
         }
     }

}