<?php
namespace Manomite\Services\Google;

class Recaptcha
{
    public $responseData;
    private $grecaptcha;
    private $secret;

    public function __construct($grecaptcha)
    {
        $this->secret = CONFIG->get('GOOGLE_RECAPTCH_PRIKEY');
        $this->grecaptcha = $grecaptcha;
    }
    public function verify($domain = null, $score = 0.7)
    {
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$this->secret.'&response='.$this->grecaptcha);
        // Decode json data
        $responseData = json_decode($verifyResponse);
        // If reCAPTCHA response is valid
        if ($responseData->success) {
            if ($score !== null and $responseData->score >= $score) {
                    return true;
                } else {
                    return false;
                }
            }else {
                return false;
            }
    }
}