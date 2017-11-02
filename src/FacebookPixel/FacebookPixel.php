<?php

namespace Eflyax\FacebookPixel;

use Nette\Application\UI\Control;

class FacebookPixel extends Control
{

    const EVENT_VIEW_CONTENT = 'ViewContent',
        EVENT_PURCHASE = 'Purchase',
        EVENT_ADD_TO_CART = 'AddToCart';

    private $sessionEvents = [
        self::EVENT_ADD_TO_CART,
        self::EVENT_VIEW_CONTENT,
        self::EVENT_PURCHASE,
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

    public function viewContent(
        $contentIds,
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

    public function addToCart(
        $contentIds,
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null
    )
    {
        $this->sendEventToOutput(
            $this->template->event = self::EVENT_ADD_TO_CART,
            $this->prepareProductsToParameters($contentIds, $contentName, $contentCategory, $value, $currency)
        );
    }

    public function purchase($value, $currency, $contentIds = null)
    {
        $this->sendEventToOutput(
            $this->template->event = self::EVENT_PURCHASE,
            $this->prepareProductsToParameters($contentIds, null, null, $value, $currency)
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
     * @return string
     */
    private function prepareProductsToParameters(
        $contentIds,
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null)
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
