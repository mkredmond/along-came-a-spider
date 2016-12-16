<?php

require 'vendor/autoload.php';

use Src\Crawler;

$crawler = new Crawler;

$crawler->domain('http://cardinal2.sjfc.edu')->tld('http://cardinal2.sjfc.edu')->run();
