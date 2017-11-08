<?php

namespace Tests\Functional;

use App\Model\ProductRepository;
use App\Presenters\HomepagePresenter;
use Eflyax\FacebookPixel\FacebookPixel;

class EventTest extends BasePresenterTest
{

    public function testPageView()
    {
        $this->checkUrlAndResponse($this->generateLink('Homepage:'));
        $this->tester->see("'init', '" . self::FB_PIXEL_ID . "'");
        $this->tester->see("'track', '" . FacebookPixel::EVENT_PAGE_VIEW . "'");
        $this->tester->seeElement('img', [
            'src' => 'https://www.facebook.com/tr?id=' . self::FB_PIXEL_ID . '&ev=PageView&noscript=1'
        ]);
    }

    public function testViewContent()
    {
        $this->checkUrlAndResponse($this->generateLink('Homepage:productDetail'));
        $this->tester->see("'track', '" . FacebookPixel::EVENT_VIEW_CONTENT . "'");
        $this->checkProduct(ProductRepository::PRODUCT_DEFAULT_ID, ProductRepository::PRODUCT_DEFAULT_PRICE);
    }

    public function testAddToCart()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:addToCart'),
            $this->generateLink('Homepage:productDetail')
        );
        $this->tester->see("'track', '" . FacebookPixel::EVENT_ADD_TO_CART . "'");
        $this->checkProduct(ProductRepository::PRODUCT_DEFAULT_ID, ProductRepository::PRODUCT_DEFAULT_PRICE);
    }

    public function testPurchase()
    {
        $this->checkUrlAndResponse($this->generateLink('Homepage:purchase'));
        $this->tester->see("'track', '" . FacebookPixel::EVENT_PURCHASE . "'");
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

    public function testSearch()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:search', ['term' => HomepagePresenter::SEARCH_TERM])
        );
        $this->tester->see("'track', '" . FacebookPixel::EVENT_SEARCH . "'");
        $this->tester->see("'search_string':'" . HomepagePresenter::SEARCH_TERM . "'");
    }

    public function testRegistration()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:registration')
        );
        $this->tester->see("'track', '" . FacebookPixel::EVENT_COMPLETE_REGISTRATION . "'");
    }

    public function testCheckout()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:checkout')
        );
        $this->tester->see("'track', '" . FacebookPixel::EVENT_INITIATE_CHECKOUT . "'");
    }

    public function testLead()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:lead')
        );
        $this->tester->see("'track', '" . FacebookPixel::EVENT_LEAD . "'");
    }

    public function testAddToWishList()
    {
        $this->checkUrlAndResponse(
            $this->generateLink('Homepage:addToWishList')
        );
        $this->tester->see("'track', '" . FacebookPixel::EVENT_ADD_TO_WISHLIST . "'");
    }

    private function checkProduct($id, $price)
    {
        $this->tester->see("'content_type':'product'");
        $this->tester->see("'content_ids':['" . $id . "']");
        $this->tester->see("'value':'" . $price . ".00'");
        $this->tester->see("'content_name':'Product " . $id . "- title'");
        $this->tester->see("'currency':'" . HomepagePresenter::CURRENCY_CODE . "'");
    }

}
