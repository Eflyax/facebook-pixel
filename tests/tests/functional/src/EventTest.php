<?php

namespace Tests\Functional;

class EventTest extends BasePresenterTest
{

    public function testPageView()
    {
        $this->checkUrlAndResponse($this->generateLink('Homepage:'));
        $this->tester->see("'init', '" . self::FB_PIXEL_ID . "'");
        $this->tester->see("'track', 'PageView'");
        $this->tester->seeElement('img', [
            'src' => 'https://www.facebook.com/tr?id=' . self::FB_PIXEL_ID . '&ev=PageView&noscript=1'
        ]);
    }

    public function testViewContent()
    {
        $this->checkUrlAndResponse($this->generateLink('Homepage:productDetail'));
        $this->tester->see("'track', 'ViewContent'");
        $this->checkProduct();
//        // we didn't call start event AddToCart before
        $this->tester->dontSee("'track', 'AddToCart'");
    }

    public function testAddToCart()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:addToCart'),
            $this->generateLink('Homepage:productDetail')
        );
        $this->tester->see("'track', 'AddToCart'");
        $this->checkProduct();
    }

    public function testPurchase()
    {
        $this->checkUrlAndResponse($this->generateLink('Homepage:purchase'));
        $this->tester->see("'track', 'Purchase'");
    }

    public function testMultipleEvents()
    {
        $this->checkUrlAndResponse($this->generateLink('Homepage:addToCartAndPurchase'));
    }

    public function testMultipleEventsWithRedirect()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:addToCartAndPurchaseWithRedirect'),
            $this->generateLink('Homepage:')
        );
    }

    private function checkProduct()
    {
        $this->tester->see("'content_type':'product'");
        $this->tester->see("'content_ids':['1']");
        $this->tester->see("'content_name':'Product title'");
        $this->tester->see("'currency':'CZK'");
    }

}
