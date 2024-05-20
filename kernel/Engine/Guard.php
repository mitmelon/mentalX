<?php
namespace Manomite\Engine;
use \Jaybizzle\CrawlerDetect\CrawlerDetect;

class Guard {
    public $CrawlerDetect;
    public function __construct(){
        $this->CrawlerDetect = new CrawlerDetect;
    }

    public function crawlerBlock(){
        if($this->CrawlerDetect->isCrawler()) {
            //crawler not allowed on this project.
            exit();
        }
    }
}