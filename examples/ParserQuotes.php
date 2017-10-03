<?php

class ParserQuotes extends \Crawler\Contracts\ParserAbstract
{
    public function parse()
    {
        $selector = '.quote .text';
        $content = $this->getBody()->__toString();
        $quotes = htmlqp((string)$content)->find($selector)->toArray();

        $filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'quotes.txt';

        foreach ($quotes as $quote) {
            $quoteString = $quote->nodeValue . "\n";
            $this->appendToFile($filePath, $quoteString);
        }
    }

    public function handleFailedRequest(\Exception $exception, $url)
    {
        echo "Failed Request: " . $url . "\n";
        echo "Error Message: " . $exception->getMessage() . "\n";
    }
}