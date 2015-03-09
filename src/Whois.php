<?php

namespace IrWhois;

class Whois
{

    /**
	 * Domain
	 *
	 * @var string
	 */
    private $domain;

    /**
     * Domain TLD
     *
     * @var string
     */
    private $tld;

    /**
     * Whois plain text result
     *
     * @var string
     */
    private $result;

    /**
     * Whois resolvers servers
     *
     * @var array
     */
    private $server = 'whois.nic.ir';

    /**
	 * Construct
     *
     * @throws InvalidArgumentException If the domain is not valid
	 * @param string $domain Domain name
	 */
    public function __construct($domain)
    {
        // Is valid?
        if (preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {

            // Store
            $this->domain = $domain;

            // TLD
            $parts = explode(".", $this->domain);
            $this->tld = strtolower(array_pop($parts));

            // Run
            $this->execute();
        }

        // Invalid domain
        if (!$this->domain) {
            throw new \InvalidArgumentException('Invalid domain');
        }
    }

    /**
     * Query DNS server
     *
     * @throws ResolveErrorException If the resver doesn't response
     * @return bool
     */
    private function execute()
    {
        try {

            // Connect
            try {
                $server = $this->server;
                $connection = fsockopen($server, 43);
            } catch (\Exception $e) {
                return false;
            }

            // Query
            fputs($connection, $this->domain."\r\n");

            // Store response
            $this->result = '';
            while (!feof($connection)) {
                $this->result .= fgets($connection);
            }

            return true;

        } catch (ResolveErrorException $e) {
            return false;
        }
    }


    /**
     * Get domain update date
     *
     * @return string|bool
     */
    public function getUpdateDate()
    {
        return $this->parseDate($this->parseText('last-updated'));
    }


    /**
     * Get domain expiration date
     *
     * @return string|bool
     */
    public function getExpirationDate()
    {
        return $this->parseDate($this->parseText('expire-date'));
    }

    /**
     * Get domain organisation
     *
     * @return string|bool
     */
    public function getOrganization()
    {
        return $this->parseText('org');
    }

    /**
     * Get domain holder fax number
     *
     * @return string|bool
     */
    public function getFaxNumber()
    {
        return $this->parseText('fax-no');
    }



    /**
     * Get domain nic handler
     *
     * @return string|bool
     */
    public function getNicHandler()
    {
        return $this->parseText('nic-hdl');
    }

    /**
     * Get domain holder address
     *
     * @return string|bool
     */
    public function getHolderAddress()
    {
        return $this->parseText('address');
    }

    /**
     * Get domain holder phone
     *
     * @return string|bool
     */
    public function getHolderPhone()
    {
        return $this->parseText('phone');
    }

    /**
     * Get domain Holder
     *
     * @return string
     */
    public function getHolder()
    {
        return str_replace('(Domain Holder)','',$this->parseText('remarks'));
    }


    /**
     * Check if domain is currenly allowing transfers
     *
     * @return bool
     */
    public function allowTransfers()
    {
        return $this->result && !strstr($this->result, 'clientTransferProhibited');
    }

    /**
     * Search text on result
     *
     * @param  string       $text
     * @param  integer      $index Match index
     * @return string|false
     */
    private function parseText($text, $index = 1)
    {
        preg_match('/'.$text.': ?(.*)/i', $this->result, $match);
        return isset($match[$index]) ? trim(preg_replace('/\s+/', ' ', $match[$index])) : false;
    }

    /**
     * Parse date to Y-m-d H:i:s format
     *
     * @param  string      $date
     * @return string|bool
     */
    private function parseDate($date)
    {
        return $date ? date('Y-m-d', strtotime($date)) : false;
    }

}
