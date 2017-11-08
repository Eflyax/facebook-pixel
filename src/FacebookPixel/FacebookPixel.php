<?php

namespace Eflyax\FacebookPixel;

use Nette\Application\UI\Control;

class FacebookPixel extends Control
{

    const EVENT_VIEW_CONTENT = 'ViewContent',
        EVENT_PURCHASE = 'Purchase',
        EVENT_ADD_TO_WISHLIST = 'AddToWishlist',
        EVENT_SEARCH = 'Search',
        EVENT_LEAD = 'Lead',
        EVENT_COMPLETE_REGISTRATION = 'CompleteRegistration',
        EVENT_INITIATE_CHECKOUT = 'InitiateCheckout',
        EVENT_ADD_TO_CART = 'AddToCart',
        EVENT_PAGE_VIEW = 'PageView';

    private $sessionEvents = [
        self::EVENT_ADD_TO_CART,
        self::EVENT_VIEW_CONTENT,
        self::EVENT_PURCHASE,
        self::EVENT_ADD_TO_WISHLIST,
        self::EVENT_LEAD,
        self::EVENT_SEARCH,
        self::EVENT_COMPLETE_REGISTRATION,
        self::EVENT_INITIATE_CHECKOUT,
    ];

    /** @var string */
    private $facebookId;

    /** @var FacebookPixelService */
    private $facebookPixelService;

    /** @var string */
    private $productIdPrefix;

    /**
     * FacebookPixel constructor.
     * @param string $id
     * @param FacebookPixelService $facebookPixelService
     * @param $productIdPrefix
     */
    public function __construct($id, FacebookPixelService $facebookPixelService, $productIdPrefix = '')
    {
        parent::__construct();
        $this->facebookId = $id;
        $this->facebookPixelService = $facebookPixelService;
        $this->productIdPrefix = $productIdPrefix;
    }

    public function render()
    {
        $this->template->id = $this->facebookId;
        $events = [];
        foreach ($this->sessionEvents as $sessionEvent) {
            $events[] = $this->facebookPixelService->getEventContent($sessionEvent);
        }
        $this->template->specialEvents = $events;
        $this->template->setFile(__DIR__ . '/templates/facebookPixel.latte');
        $this->template->render();
        $this->clearEventsFromSession();
    }

    /**
     * When a key page is viewed such as a product page, e.g. landing on a product detail page
     * @param array $contentIds
     * @param null $contentName
     * @param null $contentCategory
     * @param null $value
     * @param null $currency
     */
    public function viewContent(
        $contentIds = [],
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null
    )
    {
        $this->sendEventToOutput(
            self::EVENT_VIEW_CONTENT,
            $this->prepareProductsToParameters(
                $contentIds,
                $contentName,
                $contentCategory,
                $value,
                $currency
            )
        );
    }

    /**
     * When a product is added to the shopping cart, e.g. click on add to cart button
     * @param array $contentIds
     * @param null $contentName
     * @param null $contentCategory
     * @param null $value
     * @param null $currency
     */
    public function addToCart(
        $contentIds = [],
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null
    )
    {
        $this->sendEventToOutput(
            self::EVENT_ADD_TO_CART,
            $this->prepareProductsToParameters($contentIds, $contentName, $contentCategory, $value, $currency)
        );
    }

    /**
     * When a registration form is completed, e.g. complete subscription/signup for a service
     */
    public function completeRegistration()
    {
        $this->sendEventToOutput(
            self::EVENT_COMPLETE_REGISTRATION,
            null
        );
    }

    /**
     * When a purchase is made or checkout flow is completed, e.g. landing on thank you/confirmation page
     * @param $value
     * @param $currency
     * @param null $contentIds
     */
    public function purchase($value, $currency, $contentIds = null)
    {
        $this->sendEventToOutput(
            self::EVENT_PURCHASE,
            $this->prepareProductsToParameters($contentIds, null, null, $value, $currency)
        );
    }

    /**
     * When a person enters the checkout flow prior to completing the checkout flow, e.g. click on checkout button
     * @param null $value
     * @param null $currency
     * @param null $contentName
     * @param null $contentCategory
     * @param null $contentIds
     * @param null $contents
     * @param null $numItems
     */
    public function initiateCheckout(
        $value = null,
        $currency = null,
        $contentName = null,
        $contentCategory = null,
        $contentIds = null,
        $contents = null,
        $numItems = null
    )
    {
        $this->sendEventToOutput(
            self::EVENT_INITIATE_CHECKOUT,
            $this->prepareProductsToParameters(
                $contentIds,
                $contentName,
                $contentCategory,
                $value,
                $currency,
                $contents,
                null,
                $numItems
            )
        );
    }

    /**
     * When a search is made, e.g. when a product search query is made
     * @param null $value
     * @param null $currency
     * @param null $contentCategory
     * @param null $contentIds
     * @param null $contents
     * @param null $searchString
     */
    public function search(
        $value = null,
        $currency = null,
        $contentCategory = null,
        $contentIds = null,
        $contents = null,
        $searchString = null
    )
    {
        $this->sendEventToOutput(
            self::EVENT_SEARCH,
            $this->prepareProductsToParameters(
                $contentIds,
                null,
                $contentCategory,
                $value,
                $currency,
                $contents,
                $searchString
            )
        );
    }

    /**
     * When a sign up is completed, e.g. click on pricing, signup for trial
     * @param null $value
     * @param null $currency
     * @param null $contentName
     * @param null $contentCategory
     */
    public function lead($value = null, $currency = null, $contentName = null, $contentCategory = null)
    {
        $this->sendEventToOutput(
            self::EVENT_LEAD,
            $this->prepareProductsToParameters(
                null,
                $contentName,
                $contentCategory,
                $value,
                $currency,
                null,
                null
            )
        );
    }

    /**
     * When a product is added to a wishlist, e.g. click on add to wishlist button
     * @param null $value
     * @param null $currency
     * @param null $contentName
     * @param null $contentCategory
     * @param array $contentIds
     * @param null $contents
     */
    public function addToWishlist(
        $value = null,
        $currency = null,
        $contentName = null,
        $contentCategory = null,
        $contentIds = [],
        $contents = null
    )
    {
        $this->sendEventToOutput(
            self::EVENT_ADD_TO_WISHLIST,
            $this->prepareProductsToParameters(
                $contentIds,
                $contentName,
                $contentCategory,
                $value,
                $currency,
                $contents
            )
        );
    }

    private function sendEventToOutput($event, $eventParameters)
    {
        $output = "fbq('track', '" . $event . "'";
        if (isset($eventParameters)) {
            $output .= ', ' . $eventParameters;
        }
        $output .= ");";
        $output = str_replace('"', "'", $output);
        $this->facebookPixelService->saveEvent($event, $output);
    }

    /**
     * @param int|int[] $contentIds
     * @param string $contentName
     * @param string $contentCategory
     * @param float $value
     * @param string $currency
     * @param string $contents
     * @param null $searchString
     * @param null $numberOfItems
     * @return string
     */
    private function prepareProductsToParameters(
        $contentIds,
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null,
        $contents = null,
        $searchString = null,
        $numberOfItems = null
    )
    {
        if (!is_array($contentIds)) {
            $contentIds = [$contentIds];
        }
        $idsWithPrefix = [];
        foreach ($contentIds as $id) {
            $idsWithPrefix[] = $this->productIdPrefix . $id;
        }
        $idsWithPrefix = array_map('strval', $idsWithPrefix);
        $parameters['content_type'] = count($idsWithPrefix) > 1 ? 'product_group' : 'product';
        $parameters['content_ids'] = $idsWithPrefix;
        $parameters['content_name'] = $contentName;
        $parameters['content_category'] = $contentCategory ? $contentCategory : null;
        $parameters['value'] = $value ? $this->formatPrice($value) : null;
        $parameters['currency'] = $currency ?: null;
        $parameters['contents'] = $contents ?: null;
        $parameters['search_string'] = $searchString ?: null;
        $parameters['num_items'] = $numberOfItems ?: null;

        foreach ($parameters as $key => $value) {
            if (!$value) {
                unset($parameters[$key]);
            }
        }

        return json_encode($parameters);
    }

    private function formatPrice($value)
    {
        $value = str_replace(' ', '', $value);
        $value = str_replace(',', '.', $value);

        return number_format($value, 2, '.', '');
    }

    private function clearEventsFromSession()
    {
        foreach ($this->sessionEvents as $sessionEvent) {
            $this->facebookPixelService->removeEvent($sessionEvent);
        }
    }

}
