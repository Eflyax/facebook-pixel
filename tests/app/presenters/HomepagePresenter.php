<?php

namespace App\Presenters;

use Eflyax\FacebookPixel\FacebookPixel;
use Eflyax\FacebookPixel\FacebookPixelService;
use Eflyax\FacebookPixel\IFacebookPixelFactory;
use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter
{

    const SHIPPING_PRICE = 89;

    /** @var IFacebookPixelFactory @inject */
    public $IFacebookPixelFactory;

    /** @var FacebookPixelService @inject */
    public $facebookPixelService;

    /** @var FacebookPixel */
    public $facebookPixel;

    private $product;

    private $currencyCode = 'CZK';

    protected function startup()
    {
        parent::startup();
        $this->facebookPixel = $this['facebookPixel'];
        $product = new \stdClass();
        $product->id = 1;
        $product->price = 42;
        $product->title = 'Product title';
        $product->description = 'Product description';
        $this->product = $product;
    }

    public function actionProductDetail()
    {
        $this->facebookPixel->viewContent(
            $this->product->id,
            $this->product->title,
            null,
            $this->product->price,
            $this->currencyCode
        );
    }

    public function actionPurchase()
    {
        $totalPrice = $this->product->price + self::SHIPPING_PRICE;
        $this->facebookPixel->purchase($totalPrice, $this->currencyCode);
    }

    public function actionAddToCart()
    {
        // ..add product to shopping cart
        $this->facebookPixel->addToCart(
            $this->product->id,
            $this->product->title,
            null,
            $this->product->price,
            $this->currencyCode
        );
        $this->redirect('ProductDetail');
    }

    public function actionAddToCartAndPurchase()
    {
        $totalPrice = $this->product->price + self::SHIPPING_PRICE;
        $this->facebookPixel->addToCart(
            $this->product->id,
            $this->product->title,
            null,
            $this->product->price,
            $this->currencyCode
        );
        $this->facebookPixel->purchase($totalPrice, $this->currencyCode);
    }

    public function actionAddToCartAndPurchaseWithRedirect()
    {
        $totalPrice = $this->product->price + self::SHIPPING_PRICE;
        $this->facebookPixel->addToCart(
            $this->product->id,
            $this->product->title,
            null,
            $this->product->price,
            $this->currencyCode
        );
        $this->facebookPixel->purchase($totalPrice, $this->currencyCode);
        $this->redirect('Homepage:');
    }

    protected function createComponentFacebookPixel()
    {
        return $this->IFacebookPixelFactory->create();
    }

}
