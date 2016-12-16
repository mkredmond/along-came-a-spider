<?php

namespace Src;

class Crawler
{

    public $visited = [];

    public $queue = [];

    protected $tld;

    protected $delayInSeconds = 2; // Default value

    /**
     * Set domain to prevent spider from following external links
     * @param  string $url [description]
     * @return [type]      [description]
     */
    public function tld($tld = 'http://localhost')
    {
        $this->tld = $tld;

        return $this;
    }

    /**
     * Adds first URL to queue
     * @param  string $domain
     * @return $this
     */
    public function startUrl($url = 'http://localhost')
    {

        $this->queue[] = $url;

        return $this;
    }

    /**
     * Queues up URLs to test and adds them to the results file.
     * @return void
     */
    public function run()
    {
        $startTime = time();
        file_put_contents('urls.txt', date('l jS \of F Y h:i:s A') . "\r\n");
        while (count($this->queue) > 0) {

            $html = file_get_contents($this->queue[0]);

            array_shift($this->queue);

            $dom = new \DOMDocument;
            @$dom->loadHTML($html);

            $links = $dom->getElementsByTagName('a');

            foreach ($links as $link) {
                $href = $this->getFullUrl($link->getAttribute('href'));

                if (!in_array($href, $this->visited) && strpos($href, $this->tld) !== false) {
                    $this->queue[]   = $href;
                    $this->visited[] = $href;
                    file_put_contents('urls.txt', $href . "\r\n", FILE_APPEND);
                }
            }
            sleep($this->delayInSeconds);
        }
        $endTime = time();

        $completedTime = $endTime - $startTime;
        echo "<div style='text-align:center;'><h1>Completed in...{$completedTime} seconds</h1></div>";
    }

    /**
     * Sets the delay before attempting the next
     * http request.
     * @param integer $seconds
     * @return $this
     */
    public function setDelay($seconds = 2)
    {
        $this->delayInSeconds = $seconds;

        return $this;
    }

    /**
     * Converts relative links to absolute links
     * @param  String $href
     * @return String
     */
    protected function getFullUrl($href)
    {
        if (substr($href, 0, 7) == 'http://' || substr($href, 0, 8) == 'https://') {
            return $href;
        } else {
            return $this->tld . $href;
        }
    }
}
