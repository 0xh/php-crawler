<?php

class ParserQuotes extends \Crawler\Contracts\ParserAbstract
{
    public function parse()
    {
        $selector = '.quote .text';
        // Print Quotes.
        $content = $this->getBody()->__toString();

        $quotes = htmlqp((string)$content)->find($selector)->toArray();
        foreach ($quotes as $quote){
            echo $quote->nodeValue, "\n";
        }
    }
}