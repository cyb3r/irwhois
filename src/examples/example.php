<?php

require_once __DIR__.'/../../vendor/autoload.php';

$whois = new IrWhois\Whois(isset($argv[1]) ? $argv[1] : 'nic.ir');

echo 'Update date: ' . $whois->getUpdateDate() . PHP_EOL;

echo 'Expiration date: ' . $whois->getExpirationDate() . PHP_EOL;

echo 'Holder: ' . $whois->getHolder() . PHP_EOL;

echo 'NIC Handler: ' . $whois->getNicHandler() . PHP_EOL;

echo 'Allow transfers: '; echo $whois->allowTransfers() ? 'Yes' : 'No' . PHP_EOL;
