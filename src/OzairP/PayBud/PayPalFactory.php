<?php

namespace OzairP\PayBud;

/**
 * Class Factory
 * @package OzairP\PayBud
 */
class PayPalFactory
{

    /**
     * @return PaymentFactory
     */
    public static function CreatePayment()
    {
        return new PaymentFactory;
    }

    /**
     * @return \OzairP\PayBud\PayPalVerifier
     */
    public static function CreateVerifier()
    {
        return new PayPalVerifier;
    }

    /**
     * @param string $ClientID
     * @param string $ClientSecret
     * @param string $Mode
     *
     * @return \OzairP\PayBud\Context
     */
    public static function CreateContext($ClientID, $ClientSecret, $Mode = 'live')
    {
        return new Context($ClientID, $ClientSecret, $Mode);
    }


}
