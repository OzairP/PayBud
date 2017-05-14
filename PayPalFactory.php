<?php

namespace PayBud;

/**
 * Class Factory
 * @package PayBud
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
     * @return \PayBud\PayPalVerifier
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
     * @return \PayBud\Context
     */
    public static function CreateContext($ClientID, $ClientSecret, $Mode = 'live')
    {
        return new Context($ClientID, $ClientSecret, $Mode);
    }


}
