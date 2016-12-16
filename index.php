<?php

require 'vendor/autoload.php';

use Src\Crawler;

$crawler = new Crawler;

$crawler->setDelay(0)
    ->tld('http://wwwtest.sjfc.edu')
    ->startUrl('http://wwwtest.sjfc.edu')
    ->run();
