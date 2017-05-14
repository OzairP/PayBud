<?php

namespace OzairP\PayBud;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class PaymentFactory
{

    /**
     * @var null|Payer
     */
    protected $Payer = NULL;

    /**
     * @var array
     */
    protected $Items = [];

    /**
     * @var array
     */
    protected $Options = [
        'Currency'  => 'USD',
        'InvoiceID' => NULL,
    ];

    /**
     * @var int
     */
    protected $Shipping = 0;

    /**
     * @var float
     */
    protected $TaxRate = 0.0;

    /**
     * @var string
     */
    protected $SuccessURL = '';

    /**
     * @var string
     */
    protected $CancelURL = '';

    /**
     * @var string
     */
    protected $Description = '';

    /**
     * @var string
     */
    protected $SKU = '';

    /**
     * PaymentFactory constructor.
     */
    function __construct()
    {
        $this->Payer = new Payer();
        $this->Payer->setPaymentMethod('paypal');
        $this->SKU = uniqid();
    }

    /**
     * Add a new item. Minimum requirements for options
     * must be `Name` and `Price`, string and float respectively.
     * `Quantity` can be set as well with type int.
     *
     * @param array $ItemOpts
     *
     * @return $this
     * @throws \TypeError
     */
    public function NewItem(array $ItemOpts)
    {
        // Validation
        if(!isset($ItemOpts['Name'])) throw new \TypeError('Name option must be set.');

        if(!isset($ItemOpts['Price'])) throw new \TypeError('Price option must be set.');

        $Item = new Item();

        $Item->setName($ItemOpts['Name'])
             ->setPrice($ItemOpts['Price'])
             ->setQuantity((isset($ItemOpts['Quantity'])) ? $ItemOpts['Quantity'] : 1)
             ->setSku($this->SKU)
             ->setCurrency($this->Options['Currency']);

        $this->Items[] = $Item;

        return $this;
    }

    /**
     * Fetch the PayPal link. `URI` is the redirection
     * URL, and `PaymentID` is the payment id use for
     * verification
     *
     * @param \OzairP\PayBud\Context $Context
     *
     * @return array
     */
    public function GeneratePayPalURL(Context $Context)
    {

        $ItemList = (new ItemList)->setItems($this->Items);

        $Details = (new Details)->setShipping($this->Shipping)
                                ->setTax($this->GetTotal() * $this->TaxRate)
                                ->setSubtotal($this->GetTotal());

        $Amount = (new Amount)->setCurrency($this->Options['Currency'])
                              ->setTotal($this->GetTotal() + $this->Shipping + $this->GetTotal() * $this->TaxRate)
                              ->setDetails($Details);

        $Transaction = (new Transaction)->setAmount($Amount)
                                        ->setItemList($ItemList)
                                        ->setDescription($this->Description)
                                        ->setInvoiceNumber(($this->Options['InvoiceID'] === NULL) ? uniqid() : $this->Options['InvoiceID']);

        $URLS = (new RedirectUrls)->setReturnUrl($this->SuccessURL)
                                  ->setCancelUrl($this->CancelURL);

        $Payment = (new Payment)->setIntent('sale')
                                ->setPayer($this->Payer)
                                ->setRedirectUrls($URLS)
                                ->setTransactions([$Transaction]);

        // Unhandled exception, will be handled by user
        $Payment->create($Context);

        return [
            'URI'       => $Payment->getApprovalLink(),
            'PaymentID' => $Payment->id,
        ];
    }

    /**
     * @return int
     */
    public function GetTotal()
    {
        $Total = 0;

        foreach($this->Items as $Item) {
            $Total += $Item->getPrice() * $Item->getQuantity();
        }

        return $Total;
    }

    /**
     * @return null|Payer
     */
    public function GetPayer()
    {
        return $this->Payer;
    }

    /**
     * @return array
     */
    public function GetItems()
    {
        return $this->Items;
    }

    /**
     * @param Item $Item
     *
     * @return PaymentFactory
     */
    public function AddItem(Item $Item)
    {
        $this->Items[] = $Item;

        return $this;
    }

    /**
     * @return array
     */
    public function GetOptions()
    {
        return $this->Options;
    }

    /**
     * @param array $Opts
     */
    public function SetOptions(array $Opts = [])
    {
        $this->Options = array_merge($this->Options, $Opts);
    }

    /**
     * @return int
     */
    public function GetShippingPrice()
    {
        return $this->Shipping;
    }

    /**
     * @param int $Shipping
     *
     * @return \OzairP\PayBud\PaymentFactory
     * @throws \Exception
     */
    public function SetShippingPrice($Shipping)
    {
        if(!is_int($Shipping) && $Shipping < 0) throw new \Exception('Shipping must be a positive integer.');

        $this->Shipping = $Shipping;

        return $this;
    }

    /**
     * @return float
     */
    public function GetTaxRate()
    {
        return $this->TaxRate;
    }

    /**
     * @param $TaxRate
     *
     * @return $this
     * @throws \TypeError
     */
    public function SetTaxRate($TaxRate)
    {
        if(!is_float($TaxRate) && $TaxRate < 0.0) throw new \TypeError('Tax rate must be a positive float.');

        $this->TaxRate = $TaxRate;

        return $this;
    }

    /**
     * @return string
     */
    public function GetSuccessURL()
    {
        return $this->SuccessURL;
    }

    /**
     * @param $SuccessURL
     *
     * @return $this
     * @throws \TypeError
     */
    public function SetSuccessURL($SuccessURL)
    {
        if(!is_string($SuccessURL)) throw new \TypeError('Success URL must be a string');

        $this->SuccessURL = $SuccessURL;

        return $this;
    }

    /**
     * @return string
     */
    public function GetCancelURL()
    {
        return $this->CancelURL;
    }

    /**
     * @param $CancelURL
     *
     * @return $this
     * @throws \TypeError
     */
    public function SetCancelURL($CancelURL)
    {
        if(!is_string($CancelURL)) throw new \TypeError('Cancel URL must be a string');

        $this->CancelURL = $CancelURL;

        return $this;
    }

    /**
     * @return string
     */
    public function GetDescription()
    {
        return $this->Description;
    }

    /**
     * @param $Description
     *
     * @return $this
     * @throws \TypeError
     */
    public function SetDescription($Description)
    {
        if(!is_string($Description)) throw new \TypeError('Description must be a string');

        $this->CancelURL = $Description;

        return $this;
    }

    /**
     * @return string
     */
    public function GetSKU()
    {
        return $this->SKU;
    }

    /**
     * @param $SKU
     *
     * @return $this
     * @throws \TypeError
     */
    public function SetSKU($SKU)
    {
        if(!is_string($SKU)) throw new \TypeError('SKU must be a string');

        $this->SKU = $SKU;

        return $this;
    }

}
