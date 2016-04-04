<?php
use MxToolbox\MxToolbox;
use MxToolbox\Exceptions\MxToolboxRuntimeException;
use MxToolbox\Exceptions\MxToolboxLogicException;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../src/MxToolbox/autoload.php';

/**
 * Class myOwnBlackList
 */
class myOwnBlackList extends MxToolbox
{

    /** @var array - my own blacklist with DNSBL host names array */
    public $myBlacklist = array();

    /**
     * easyTest constructor.
     */
    public function __construct($myBlacklist)
    {
        if (is_array($myBlacklist) && count($myBlacklist) > 0)
            $this->myBlacklist = $myBlacklist;
        // MxToolbox construct
        parent::__construct();
    }

    /**
     * Configure MXToolbox
     * configure() is abstract function and must by implemented
     */
    public function configure()
    {
        $this
            // path to the dig tool
            ->setDig('/usr/bin/dig')
            // multiple resolvers is allowed
            //->setDnsResolver('8.8.8.8')
            //->setDnsResolver('8.8.4.4')
            ->setDnsResolver('127.0.0.1')
            // load your own blacklist array (will be auto validate on DNSBL response)    
            ->setBlacklists($this->myBlacklist);
    }

    /**
     * Test IP address or domain name - with my own blacklists
     * @param string $addr
     */
    public function testMyIPAddress($addr)
    {

        /*
         * Get test array prepared for check (without any test results)
         */
        //var_dump($this->getBlacklistsArray());

        /*
         * Check IP address on all DNSBL
         */
        $this->checkIpAddressOnDnsbl($addr);

        /*
         *  Get the same array but with a check results
         * 
         *  Return structure:
         *  []['blHostName'] = dnsbl hostname
         *  []['blPositive'] = true if IP address have the positive check
         *  []['blPositiveResult'] = array() array of a URL addresses if IP address have the positive check
         *  []['blResponse'] = true if DNSBL host name is alive or DNSBL responded during the test
         *  []['blQueryTime'] = false or response time of a last dig query
         */

        var_dump($this->getBlacklistsArray());

        /*
         * Cleaning old results - REQUIRED only in loop before next test
         * TRUE = check responses for all DNSBL again (default value)
         * FALSE = only cleaning old results ([blResponse] => true)
         */
        $this->cleanBlacklistArray(false);

    }

}

$myBlacklist = array(
    0 => 'zen.spamhaus.org',
    1 => 'xbl.spamhaus.org'
);

try {
    $test = new myOwnBlackList($myBlacklist);
    $test->testMyIPAddress('8.8.8.8');

} catch (MxToolboxRuntimeException $e) {
    echo $e->getMessage();
} catch (MxToolboxLogicException $e) {
    echo $e->getMessage();
}
