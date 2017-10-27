#Nette extension for Facebook Pixel.

## Requirements

- [Nette/Application](https://github.com/nette/application)

## Installation

The best way to install Eflyax/Facebook-pixel is using  [Composer](http://getcomposer.org/):

```sh
$ composer require eflyax/facebook-pixel
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
    productIdPrefix: '42' // optional
``` 

Be careful you add FB pixel ID as string, there was issues with integer

#### Backend

Inject FB pixel factory and service to your module where you want to render FB pixel. FB pixel service
will help you to render specific events.

```
abstract class BaseFrontPresenter extends BasePresenter
{

    /** @var IFacebookPixelFactory @inject */
    public $facebookPixelFactory;
    
    /** @var FacebookPixelService @inject */
    public $facebookPixelService;
    
    .
    .
    
    protected function createComponentFacebookPixel()
    {
        return $this->facebookPixelFactory->create();
    }
    
}
```
#### Frontend

Now you are ready to render basic FB pixel for event PageView. Render control faceboobPixel in layout

`{control facebookPixel}`


### Events

#### AddToCart

##### Backend
In method where you add product to cart call eventStart like this:

````
    public function actionAddProduct($idProduct, $quantity = 1)
    {
        ...
        $this->facebookPixelService->eventStart(FacebookPixelService::EVENT_ADD_TO_CART);
    }

````

##### Frontend
This control is rendered only if you call eventStart addToCart on backend. When you render this control event is automatically deactivated
 
```
{control facebookPixel:addToCart,
    $product->getId(),
    $product->getTitle(),
    $product->getDescription(),
    $product->getPrice(),
    $currency->getCode()
}
```

#### Purchase

##### Backend
Before you redirect customer on thank you page call startEvent:

`$this->facebookPixelService->eventStart(FacebookPixelService::EVENT_PURCHASE);`

##### Frontend

```
{control facebookPixel:purchase,
    $totalPrice,
    $currency->getCode(),
    $itemIds
}
```

`$itemIds` can contains id for one or more products 


#### ViewContent
##### Frontend
For one product:
```
{control facebookPixel:viewContent,
    $product->getId(),
    $product->getTitle(),
    $product->getDescription(),
    $product->getPrice(),
    $currency->getCode()
}
```
For more products (category):
```
{control facebookPixel:viewContent,
    $productIds
}
```

## Events validation

If you want to validate events sended to facebook I can recommend this browser plugin: 
[Facebook Pixel Helper](https://chrome.google.com/webstore/detail/FacebookPixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc)
