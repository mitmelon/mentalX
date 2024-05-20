<?php
namespace Manomite\Engine\Bayes\Tokenizer;
use Manomite\Engine\Bayes\TokenizerInterface;

class WhitespaceAndPunctuationTokenizer implements TokenizerInterface
{
    protected $pattern = "/[ ,.?!-:;\\n\\r\\t…_]/u";

    public function tokenize($string)
    {
        $retval = preg_split($this->pattern, mb_strtolower($string, 'utf8'));
        $retval = array_filter($retval, 'trim');
        $retval = array_values($retval);

        return $retval;
    }
}
