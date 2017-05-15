<?php

namespace OzairP\PayBud;

use PayPal\Api\Payment;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

/**
 * Class PayPalVerifier
 * @package OzairP\PayBud
 */
class PayPalVerifier
{

    /**
     * Verify a payment. Must be called with GET
     * params set `paymentId`, `token`, `PayerID`
     * This is called where the success url is hosted
     *
     * @param string                                         $PaymentID
     * @param \OzairP\PayBud\Context|\PayPal\Rest\ApiContext $Context
     *
     * @return bool
     * @throws \OzairP\PayBud\VerificationException
     * @throws \TypeError
     */
    public function Verify($PaymentID, ApiContext $Context)
    {
        // Validation
        if(!isset($_GET['paymentId'])) throw new VerificationException('paymentID not set.');

        if(!isset($_GET['token'])) throw new VerificationException('token not set.');

        if(!isset($_GET['PayerID'])) throw new VerificationException('PayerID not set.');

        // Make sure it's a string OR a traversable/array of strings.
        if(!is_string($PaymentID)) throw new \TypeError('PaymentID must be a string.');

        $Payment = NULL;

        try {
            $Payment = Payment::get($_GET['paymentId'], $Context);
        } catch(PayPalConnectionException $e) {
            throw new VerificationException('Invalid paymentId');
        }

        return $Payment->id === $PaymentID;
    }

}