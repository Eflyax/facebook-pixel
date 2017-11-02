# Nette extension for Facebook Pixel.

TODO
* implement missing events (search, addToWishList, etc..)
* support for ajax

## Requirements

- [Nette/Application](https://github.com/nette/application)

## Installation

The best way to install Eflyax/Facebook-pixel is using  [Composer](http://getcomposer.org/):

```bash
$   composer require eflyax/facebook-pixel
```

## Usage

### Preparation
#### Extension registration
Add to your neon config:
```
extensions:
    facebookPixel: Eflyax\FacebookPixel\DI\FacebookPixelExtension
```
and configuration with you FB pixel ID:
```
facebookPixel:
    id: '111122223333444'
    productIdPrefix: '42' # optional
``` 

Be careful you add FB pixel ID as string, there was issues with integer

#### Backend

Inject FB pixel factory and service to your module where you want to render FB pixel. FB pixel service
will help you to render specific events.

```php
abstract class BaseFrontPresenter extends BasePresenter
{

    /** @var IFacebookPixelFactory @inject */
    public $IFacebookPixelFactory;
    
    /** @var FacebookPixel */
    public $facebookPixel;
    
    
    protected function startup()
    {
        parent::startup();
        $this->facebookPixel = $this['facebookPixel'];
    }

    
    .
    .
    
    protected function createComponentFacebookPixel()
    {
        return $this->IFacebookPixelFactory->create();
    }
    
}
```
#### Frontend

Now you are ready to render FB pixel in layout

`{control facebookPixel}`


### Events

#### AddToCart

In method where you add product to cart call eventStart like this:

````php
    $this->facebookPixel->addToCart(
        $productId,
        $productTitle,
        $productCategory,
        $productPrice,
        $currencyCode
    );

````

#### Purchase

```
  $this->facebookPixel->purchase($totalPrice, $currencyCode);
```
#### ViewContent

For one product:
```php
$this->facebookPixel->viewContent(
    $productId,
    $productTitle,
    $productCategory,
    $productPrice,
    $currencyCode
);
```
For more products (category):

```php
$this->facebookPixel->viewContent(
    $productIds
);
```

## Events validation

If you want to validate events which you send to facebook I can recommend this browser plugin: 
[Facebook Pixel Helper](https://chrome.google.com/webstore/detail/FacebookPixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc)


## How to run tests
```bash
$   cd tests
$   composer install
$   mkdir temp
$   ./vendor/bin/codecept build
$   ./vendor/bin/codecept run
```


