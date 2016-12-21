<?php 

require 'index.php';

$crawler->setDelay(1)
    ->tld('www.sjfc.edu')
    ->startUrl('http://www.sjfc.edu')
    ->run();