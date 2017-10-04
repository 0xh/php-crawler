<?php

namespace Crawler\Contracts;

use Crawler\Core;
use Crawler\Exceptions\FileException;
use Crawler\Exceptions\ParameterException;
use Psr\Http\Message\ResponseInterface;

abstract class ParserAbstract implements ParserInterface
{
    /**
     * @link https://fileinfo.com/filetypes/common
     * @var array
     */
    public $assertExtensions = [
        'text' => [
            'doc',
            'docx',
            'text',
            'log',
        ],
        'data' => [
            'csv',
            'ppt',
            'pptx',
            'pdf',
        ],
        'audio' => [
            'mp3',
            'wav',
        ],
        'video' => [
            'avi',
            'flv',
            'mp4',
        ],
        'image' => [
            'bmp',
            'gif',
            'jpg',
            'png',
            'psd',
            'svg',
        ],
    ];
    /**
     * @var ResponseInterface $response
     */
    protected $response;

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getBody()
    {
        return $this->response->getBody();
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    abstract public function parse();

    /**
     * Grab all links that matches the site URL.
     *
     * @param $bodyContent
     * @param $siteUrl
     * @return array
     */
    protected function matchNewURLs($bodyContent, $siteUrl)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($bodyContent);
        $links = $dom->getElementsByTagName('a');

        $host = getUrlHost($siteUrl);
        $urls = [];
        foreach ($links as $link) {
            // Todo if attribute exists
            $url = $link->getAttribute('href');

            if ($url[0] === '/') {
                $urls[] = $siteUrl . $url;
                continue;
            }

            if (hasSameHost($host, $url)) {
                $urls[] = $url;
            }
        }

        $urls = $this->removeDuplicatedUrls($urls);

        foreach ($urls as $key => $url) {
            if (app(Core::class)->fetchedLinkPool->isExist($url)) {
                unset($urls[$key]);
            }
        }

        return $urls;
    }

    protected function removeAssertUrls($urls, $assertExtensions = [])
    {
        if (empty($assertExtensions)) {
            $assertExtensions = call_user_func_array('array_merge', $this->assertExtensions);
        }

        foreach ($urls as $key => $url) {
            $parts = pathinfo($url);
            if (isset($parts['extension']) &&
                in_array($parts['extension'], $assertExtensions)
            ) {
                unset($urls[$key]);
            }
        }

        return $urls;
    }

    /**
     * Grab all new URLs and insert to the URL Pool.
     *
     * If you want to filter some types of URLs,
     * please extent this class and rewrite this function.
     *
     * @param $siteUrl
     */
    public function grabNewUrlsForWholeSiteFetch($siteUrl)
    {
        $urls = $this->matchNewURLs($this->getBody()->__toString(), $siteUrl);
        $this->addNewUrls($urls);
    }

    protected function addNewUrls($urls)
    {
        app(Core::class)->linkPool->add($urls);
    }

    protected function removeDuplicatedUrls(array $urls)
    {
        $urls = array_map(function ($url) {
            return $this->filterFragment($url);
        }, $urls);

        return array_unique($urls);
    }

    //https://www.example.com/page/1/#comments
    protected function filterFragment($url)
    {
        $numberSignPosition = strpos($url, '#');
        if ($numberSignPosition !== false) {
            return substr($url, 0, $numberSignPosition);
        }

        return $url;
    }

    public function handleFailedRequest(\Exception $exception, $url)
    {
        //
    }

    public function handleDownloadSuccessfullyRequest($url)
    {
        //
    }

    public function handleDownloadFailedRequest(\Exception $exception, $url)
    {
        //
    }

    public function appendToFile($filePath, $content)
    {
        if (!is_string($content)) {
            throw new ParameterException('Parameter type of content must be String');
        }
        $handle = fopen($filePath, 'a') or new FileException("Can't open " . $filePath);
        fwrite($handle, $content);
        fclose($handle);
    }
}