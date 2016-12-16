<?php

namespace Src;

class Crawler {

    public $visited = [];

    public $queue = [];

    protected $domain = 'http://localhost';

    public function tld($url = 'http://localhost'){
        $this->queue[] = $url;

        return $this;
    }

    public function domain($domain = 'http://localhost') {
        $this->domain = $domain;

        return $this;
    }

    public function run(){
        $startTime = time();
        file_put_contents('urls.txt', date('l jS \of F Y h:i:s A') . "\r\n");
        while ( count($this->queue) > 0)  {
            
            $html = file_get_contents($this->queue[0]);

            array_shift($this->queue);

            $dom = new \DOMDocument;
            @$dom->loadHTML($html);

            $links = $dom->getElementsByTagName('a');

            foreach ($links as $link) {
                $href = $this->getFullUrl($link->getAttribute('href'));

                if (!in_array($href, $this->visited) && strpos($href, $this->domain) !== false ) {
                    $this->queue[] = $href;
                    $this->visited[] = $href;
                    file_put_contents('urls.txt', $href . "\r\n", FILE_APPEND);
                }
            }
            sleep(2);
        }
        $endTime = time();

        $completedTime = $endTime - $startTime;
        echo "<div style='text-align:center;'><h1>Completed in...{$completedTime} seconds</h1></div>";
    }

    protected function getFullUrl($href) {
         if (substr($href, 0, 7) == 'http://' || substr($href,0,8) == 'https://') {
            return $href;
        } else {
            return $this->domain . $href;
        }
    }
}