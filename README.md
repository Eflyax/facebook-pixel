#Nette extension for Facebook Pixel.

# Registrace rozšíření
V **extensions.neon** přidáme *facebookPixel*

```
extensions:
    facebookPixel: Eflyax\Facebook-pixel\DI\FacebookPixelExtension
```

A konfiguraci:
```
facebookPixel:
    id: '111122223333444'
```
Id musí být v podobě stringu

# Integrace do projektu

## Registrace  továrničky pro komponentu

V modulu (případně BasePresenteru), kde chceme kód generovat si vstříkneme továrničku:

```
  /** @var IFacebookPixelFactory @inject */
    public $facebook_pixel_factory;
```

k tomu také metodu na vytvoření komponenty:

```
    protected function createComponentFacebookPixel()
    {
        return $this->facebook_pixel_factory->create();
    }
```

Nyní můžeme v layoutu webu vykreslit základní komponentu, která provede odesílání výchozí události pageView a umožní používat ostaní události jako viewContent, Puchase, AddToCart, apod..

`{control facebookPixel}`

# Událost AddToCart
Rozšíření podporuje jak odesílání produktu do košíku přes ajax, tak vložení do košíku s novým načtením stránky.

## Vložení do košíku přes ajaxový požadavek

```
{control facebookPixel:addToCart,
    $product->getId(),
    $product->getTitle(),
    $product->getDescription(),
    $product->getPrice(),
    $currency->getCode(),
    '#buttonAddToCart'
}
```
Poslední parametr provede bindování na tlačítko (událost onClick). Lze ho nabindovat jak na tlačítko s třídou **.buttonAddToCart** tak na tlačítko s identifikátorem **#buttonAddToCart**

## Vložení košíku s přechodem na novou stránku

V presenteru, kde probíhá vkládání do košíku si vstříkneme službu   **FacebookPixelService**:
```
 /** @var FacebookPixelService @inject */
    public $facebookPixelService;
```
V metodě, kde proběhlo vložení do košíku je potřeba komponentu zaktivovat:

`        $this->facebookPixelService->eventStart(FacebookPixelService::EVENT_ADD_TO_CART);`

Na stránce, kam proběhlo přesměrování vykreslíme komponentu:
```
{control facebookPixel:addToCart,
    $product->getId(),
    $product->getTitle(),
    $product->getDescription(),
    $product->getPrice(),
    $currency->getCode()
}
```
Při vykreslení se komponenta sama deaktivuje, při znovunačtení stránky se nevykreslí, je nutné opět vložit produkt do košíku, čímž proběhne nová aktivace.


# Událost Purchase

Rozšíření podporuje odesílání jednoho či více zakoupených produktů. Při odeslání objednávky je potřeba zaktivovat komponentu pro odeslání této události.
V Presenteru, kde probíhá odesálíní objednávky si vstříkneme službu **FacebookPixelService**:
```
    /** @var FacebookPixelService @inject */
    public $facebookPixelService;
```
Následně v metodě kde probíhá odesílání objednávky provedeme aktivaci komponenty:

`$this->facebookPixelService->eventStart(FacebookPixelService::EVENT_PURCHASE);`

Na stránce, kam nás odeslání objednávky přesměrovalo vykreslíme komponentu:
```
{control facebookPixel:purchase,
    $cartTotal,
    $currency->getCode(),
    $item_ids
}
```
`$item_ids` může být ID jednoho produktu, nebo pole ID více produktů 


# Událost ViewContent
Na stránce zobrazující jeden či více produktů vykreslíme komponentu:

## Pro jeden produkt
```
{control facebookPixel:viewContent,
    $product->getId(),
    $product->getTitle(),
    $product->getDescription(),
    $product->getPrice(),
    $currency->getCode()
}
```

## Pro více produktů pošleme parametrem pole s ID produktů
```
{control facebookPixel:viewContent,
    $productIds
}
```

# Validace událostí

Pro validaci událostí je vhodné rozšíření do prohlížeče [Facebook Pixel Helper](https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc)
