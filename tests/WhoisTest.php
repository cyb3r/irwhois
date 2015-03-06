<?php

require_once __DIR__.'/../vendor/autoload.php';

use Arall\Whois;

class WhoisTest extends PHPUnit_Framework_TestCase
{

    public function testInvalidDomain()
    {
        $this->setExpectedException('InvalidArgumentException');

        new Whois('invalid-domain');
    }

    public function testResolver()
    {
        $whois = new Whois('eff.org');

        $this->assertEquals('1990-10-10 05:00:00', $whois->getCreationDate());
        $this->assertEquals('2013-08-26 20:31:10', $whois->getUpdateDate());
        $this->assertEquals('2022-10-09 06:00:00', $whois->getExpirationDate());
        $this->assertEquals('Gandi SAS (R42-LROR)', $whois->getRegistrar());
        $this->assertEquals('D2234962-LROR', $whois->getId());
        $this->assertFalse($whois->allowTransfers());
    }
}
