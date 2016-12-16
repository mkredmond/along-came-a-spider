<?php

require 'vendor/autoload.php';

use Src\Crawler;

$crawler = new Crawler;

$crawler->tld('http://localhost')->startUrl('http://localhost')->run();
