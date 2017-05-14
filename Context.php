<?php
/**
 * Created by PhpStorm.
 * User: ozairpatel
 * Date: 5/14/17
 * Time: 4:40 AM
 */

namespace PayBud;


use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Context
{

    protected $PayPalAPIContext = NULL;

    function __construct($ClientID, $ClientSecret, $Mode = 'live')
    {

        if($Mode !== 'live' || $Mode !== 'sandbox') throw new \Exception('Mode must be \'live\' or \'sandbox\'');

        $this->PayPalAPIContext = new ApiContext(new OAuthTokenCredential($ClientID, $ClientSecret));

        $this->PayPalAPIContext->setConfig([
            'mode' => $Mode,
        ]);

    }

    public function GetContext()
    {
        return $this->PayPalAPIContext;
    }

}