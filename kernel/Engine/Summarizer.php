<?php
namespace Manomite\Engine;
use \PhpScience\TextRank\{
    Tool\StopWords\English,
    TextRankFacade
};

class Summarizer{
    public function summarize($content){
        return (new \DivineOmega\PHPSummary\SummaryTool($content))->getSummary();
    }

    public function getKeywords($text){
        $api = new TextRankFacade();
        $stopWords = new English();
        $api->setStopWords($stopWords);
        $result = $api->getOnlyKeyWords($text); 
        return $result;
    }
}