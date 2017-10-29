<?php

namespace App\Presenters;

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

    private $product;

    protected function startup()
    {
        parent::startup();
        $product = new \stdClass();
        $product->id = 1;
        $product->price = 42;
        $product->title = 'Product title';
        $product->description = 'Product description';

        $this->product = $product;
    }

    public function actionProductDetail()
    {
        $this->template->product = $this->product;
        $this->template->currencyCode = 'CZK';
    }

    public function actionPurchase()
    {
        $this->template->totalPrice = $this->product->price + self::SHIPPING_PRICE;
        $this->template->currencyCode = 'CZK';
        $this->template->productIds = [$this->product->id];
        $this->facebookPixelService->eventStart(FacebookPixelService::EVENT_PURCHASE);
    }

    public function actionAddToCart()
    {
        // ..add product to shopping cart
        $this->facebookPixelService->eventStart(FacebookPixelService::EVENT_ADD_TO_CART);
        $this->redirect('ProductDetail');
    }

    protected function createComponentFacebookPixel()
    {
        return $this->IFacebookPixelFactory->create();
    }

}
