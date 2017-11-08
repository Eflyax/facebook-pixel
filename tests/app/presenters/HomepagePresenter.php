<?php

namespace App\Presenters;

use App\Model\ProductRepository;
use Eflyax\FacebookPixel\FacebookPixel;
use Eflyax\FacebookPixel\FacebookPixelService;
use Eflyax\FacebookPixel\IFacebookPixelFactory;
use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter
{

    const CURRENCY_CODE = 'CZK';
    const SHIPPING_PRICE = 89;
    const SEARCH_TERM = 'Something to search';

    /** @var IFacebookPixelFactory @inject */
    public $IFacebookPixelFactory;

    /** @var FacebookPixelService @inject */
    public $facebookPixelService;

    /** @var ProductRepository @inject */
    public $productRepository;

    /** @var FacebookPixel */
    public $facebookPixel;

    private $product;

    protected function startup()
    {
        parent::startup();
        $this->facebookPixel = $this['facebookPixel'];
        $this->product = $this->productRepository
            ->getProduct(ProductRepository::PRODUCT_DEFAULT_ID, ProductRepository::PRODUCT_DEFAULT_PRICE);
    }

    public function actionProductDetail()
    {
        $this->facebookPixel->viewContent(
            $this->product->id,
            $this->product->title,
            null,
            $this->product->price,
            self::CURRENCY_CODE
        );
    }

    public function actionPurchase()
    {
        $totalPrice = $this->product->price + self::SHIPPING_PRICE;
        $this->facebookPixel->purchase($totalPrice, self::CURRENCY_CODE);
    }

    public function actionAddToCart()
    {
        $this->facebookPixel->addToCart(
            $this->product->id,
            $this->product->title,
            null,
            $this->product->price,
            self::CURRENCY_CODE
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
            self::CURRENCY_CODE
        );
        $this->facebookPixel->purchase($totalPrice, self::CURRENCY_CODE);
    }

    public function actionAddToCartAndPurchaseWithRedirect()
    {
        $totalPrice = $this->product->price + self::SHIPPING_PRICE;
        $this->facebookPixel->addToCart(
            $this->product->id,
            $this->product->title,
            null,
            $this->product->price,
            self::CURRENCY_CODE
        );
        $this->facebookPixel->purchase($totalPrice, self::CURRENCY_CODE);
        $this->redirect('Homepage:');
    }

    public function actionSearch($term)
    {
        $this->facebookPixel->search(null, null, null, null, null, $term);
        $this->setView('default');
    }

    public function actionRegistration()
    {
        $this->facebookPixel->completeRegistration();
        $this->setView('default');
    }

    public function actionCheckout()
    {
        $this->facebookPixel->initiateCheckout();
        $this->setView('default');
    }

    public function actionLead()
    {
        $this->facebookPixel->lead();
        $this->setView('default');
    }

    public function actionAddToWishList()
    {
        $this->facebookPixel->addToWishlist();
        $this->setView('default');
    }

    protected function createComponentFacebookPixel()
    {
        return $this->IFacebookPixelFactory->create();
    }

}
