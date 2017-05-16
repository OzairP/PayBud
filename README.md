# PayBud - PayPal Payments Factory

## Installation
```
composer require OzairP/PayBud
```

```PHP
require('./vendor/autoload.php')
```

## Usage

```PHP
use OzairP\PayBud\PayPalFactory;

$Context = PayPalFactory::CreateContext('CLIENTID', 'CLIENTSECRET', 'sandbox');
           
$FactoryResult = PayPalFactory::CreatePayment()
                     ->SetSuccesURL('web.com?ok')
                     ->SetCancelURL('web.com?bad')
                     ->SetDescription('Buy Bits')
                     ->NewItem([
                         'Name' => 'Bit',
                         'Price' => 1.0
                     ])
                     ->GeneratePayPalURL($Context);
                     
/*
 * $FactoryResult['PaymentID'] is used for verification later.
 * You should save this in the session or elsewhere
 */
 
SomeHowStore($FactoryResult['PaymentID']);

SomeHowRedirect($FactoryResult['URI']);

// When the success URL is called
$Result = false;
try {
    // PaymentID is loaded from the previous call
    $Result = PayPalFactory::CreateVerifier
                ->Verify(($PaymentID, $Context);
} catch(VerificationException $e) {
    // Handle exception
}

if($Result)
    // Handle a good call
```