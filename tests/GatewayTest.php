<?php

namespace Omnipay\Monetico\Tests;

use DansMaCulotte\Monetico\Requests\PaymentRequest;
use Omnipay\Common\CreditCard;
use Omnipay\Monetico\Messages\PurchaseResponse;
use Omnipay\Monetico\Gateway;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var Gateway */
    protected $gateway;

    /** @var CreditCard */
    protected $card;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setEptCode('1234567');
        $this->gateway->setSecurityKey('1036DEFD9175F29EE9337A1F284BDF781D5A4048');
        $this->gateway->setCompanyCode('dmc');
        $this->gateway->setTestMode(true);

        $this->card = new CreditCard([
            'firstName' => 'John',
            'lastName' => 'English',
            'email' => 'john@english.fr',
        ]);
    }

    public function testPurchase()
    {
        /** @var PurchaseResponse $response */
        $response = $this->gateway->purchase([
            'transactionId' => 'DMC123456789',
            'language' => 'FR',
            'amount' => '10.00',
            'currency' => 'EUR',
            'card' => $this->card,
            'returnUrl' => 'http://localhost/success',
            'cancelUrl' => 'http://localhost/error',
        ])->send();

        $this->assertInstanceOf(PurchaseResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotNull($response->getRedirectUrl());
        $this->assertSame(PaymentRequest::getUrl(true), $response->getRedirectUrl());
        $this->assertTrue($response->isTransparentRedirect());
    }
}