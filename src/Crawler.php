<?php

namespace src;

class Crawler
{

    public $visited = [];

    public $queue = [];

    protected $tld;

    protected $stop = false;

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
        $counter = 1;
        file_put_contents('urls.txt', date('l jS \of F Y h:i:s A') . "\r\n");
        while (!empty($this->queue) > 0 && !$this->stop) {

            $this->checkStop();

            $html = file_get_contents($this->queue[0]);

            array_shift($this->queue);

            $dom = new \DOMDocument;
            @$dom->loadHTML($html);
            $links = $dom->getElementsByTagName('a');

            foreach ($links as $link) {
                $href = $this->prepareUrl($link->getAttribute('href'));

                if ($href && !in_array($href, $this->visited) && strpos($href, $this->tld) !== false) {
                    $this->queue[]   = $href;
                    $this->visited[] = $href;
                    file_put_contents('urls.txt', $href . "\r\n", FILE_APPEND);
                }

                echo "[" . time() . "] #{$counter} - Current Queue: " . count($this->queue) . " || Url: {$href}\r\n";
                $counter++;
            }
            sleep($this->delayInSeconds);
        }
        $endTime = time();

        $completedTime = $endTime - $startTime;
        // echo "<div style='text-align:center;'><h1>Completed in...{$completedTime} seconds</h1></div>";
        echo "Completed in...{$completedTime} seconds";
    }

    /**
     * Empties queue to stop process
     * @return
     */
    public function stop()
    {
        unset($this->queue);
        $this->queue = [];
        $this->stop  = true;
        die('stopped');
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
    protected function prepareUrl($href)
    {
        $href = parse_url($href);

        if ((isset($href['scheme']) && ($href['scheme'] !== 'http' || $href['scheme'] !== 'https'))
            || isset($href['path']) && (
                (strpos(strtolower($href['path']), '.jpg') !== false 
                || strpos(strtolower($href['path']), '.pdf') !== false
                || strpos(strtolower($href['path']), '.jpeg') !== false
                || strpos(strtolower($href['path']), '.png') !== false
                || strpos(strtolower($href['path']), '.doc') !== false
                || strpos(strtolower($href['path']), '.docx') !== false
                || strpos(strtolower($href['path']), '.ppt') !== false
                || strpos(strtolower($href['path']), '.pptx') !== false
                || strpos(strtolower($href['path']), '.xls') !== false
                )
        )) {
            return false;
        }

        if (isset($href['host']) && $href['host'] == $this->tld) {
            return "http://{$href['host']}/" . ltrim($href['path'], '/');
        } else if (!isset($href['host'])) {
            return "http://{$this->tld}/" . ltrim($href['path'], '/');
        }
    }

    /**
     * Dies and exits if the stop command has been sent
     * @return  void
     */
    protected function checkStop()
    {
        if ($this->stop) {
            die(exit());
        }
    }
}
