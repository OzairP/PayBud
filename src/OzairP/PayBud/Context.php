<?php

namespace OzairP\PayBud;


use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

/**
 * Class Context
 * @package OzairP\PayBud
 */
class Context extends ApiContext
{

    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE = 'live';

    /**
     * Context constructor.
     *
     * @param string $ClientID
     * @param string $ClientSecret
     * @param string $Mode
     * @param string   $LogFileLocation
     *
     * @throws \TypeError
     */
    function __construct($ClientID, $ClientSecret, $Mode = Context::MODE_LIVE, $LogFileLocation = NULL)
    {

        if($Mode !== 'live' && $Mode !== 'sandbox') throw new \TypeError('Mode must be \'live\' or \'sandbox\'');

        parent::__construct(new OAuthTokenCredential($ClientID, $ClientSecret));

        $this->setConfig([
            'mode'           => $Mode,
            'log.LogEnabled' => !is_null($LogFileLocation),
            'log.FileName'   => $LogFileLocation,
            'log.LogLevel'   => ($Mode === Context::MODE_LIVE) ? 'INFO' : 'DEBUG',
        ]);

    }

}