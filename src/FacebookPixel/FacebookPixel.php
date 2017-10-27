<?php

namespace Eflyax\FacebookPixel;

use Nette\Application\UI\Control;

/**
 * Class FacebookPixel
 * @package Eflyax\FacebookPixel
 * Main class of FacebookPixel control
 */
class FacebookPixel extends Control
{

    /** @var String */
    private $facebookId;

    /** @var FacebookPixelService */
    private $facebookPixelService;

    /**
     * FacebookPixel constructor.
     * @param String $id
     * @param FacebookPixelService $facebookPixelService
     */
    public function __construct($id, FacebookPixelService $facebookPixelService)
    {
        parent::__construct();
        $this->facebookId = $id;
        $this->facebookPixelService = $facebookPixelService;
    }

    /**
     * Render code for event pageView
     */
    public function render()
    {
        $this->template->id = $this->facebookId;
        $this->template->setFile(__DIR__ . '/templates/facebookPixel.latte');
        $this->template->render();
    }

    /**
     * Render code for event viewContent
     * @param int|int[] $contentIds
     * @param String $contentName
     * @param String $contentCategory
     * @param float $value
     * @param String $currency
     * @return String
     */
    public function renderViewContent(
        $contentIds,
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null
    )
    {
        $this->template->parameters =
            $this->prepareProductsToParameters($contentIds, $contentName, $contentCategory, $value, $currency);
        $this->template->event = FacebookPixelService::EVENT_VIEW_CONTENT;
        $this->template->id = $this->facebookId;
        $this->template->render(__DIR__ . '/templates/commonEvent.latte');
    }

    /**
     * Render code for event addToCart
     * @param int|int[] $contentIds
     * @param String $contentName
     * @param String $contentCategory
     * @param float $value
     * @param String $currency
     * @param String $selector
     */
    public function renderAddToCart(
        $contentIds,
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null,
        $selector = null
    )
    {
        // Do nothing if both event "add to cart" and selector are null
        if (!$this->facebookPixelService->eventStatus(FacebookPixelService::EVENT_ADD_TO_CART) && !$selector) {
            return;
        }
        $this->template->selector = $selector;
        $this->template->id = $this->facebookId;
        $this->template->event = FacebookPixelService::EVENT_ADD_TO_CART;
        $this->template->parameters =
            $this->prepareProductsToParameters($contentIds, $contentName, $contentCategory, $value, $currency);
        $this->template->render(__DIR__ . '/templates/addToCart.latte');
        if (!$selector) {
            $this->facebookPixelService->eventEnd(FacebookPixelService::EVENT_ADD_TO_CART);
        }
    }

    /**
     * Render code for event purchase on successful payment
     * @param String $value Total value of purchase
     * @param String $currency
     * @param int|int[] $contentIds
     */
    public function renderPurchase($value, $currency, $contentIds = null)
    {
        if (!$this->facebookPixelService->eventStatus(FacebookPixelService::EVENT_PURCHASE)) {
            return;
        }
        $parameters['value'] = $value;
        $parameters['currency'] = $currency;
        $parameters['content_type'] = count($contentIds) > 1 ? 'product_group' : 'product';
        $parameters['content_ids'] = $contentIds;
        $this->template->parameters = json_encode($parameters);
        $this->template->event = FacebookPixelService::EVENT_PURCHASE;
        $this->template->render(__DIR__ . '/templates/commonEvent.latte');
        $this->facebookPixelService->eventEnd(FacebookPixelService::EVENT_PURCHASE);
    }

    /**
     * Prepare products information for template
     * @param int|int[] $contentIds
     * @param String $contentName
     * @param String $contentCategory
     * @param float $value
     * @param String $currency
     * @return String
     */
    private function prepareProductsToParameters(
        $contentIds,
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null)
    {
        $parameters['content_type'] = count($contentIds) > 1 ? 'product_group' : 'product';
        $parameters['content_ids'] = $contentIds;
        $parameters['content_name'] = $contentName;
        $parameters['content_category'] = $contentCategory ? $contentCategory : null;
        $parameters['value'] = $value ? $value : null;
        $parameters['currency'] = $currency ? $currency : null;

        return json_encode($parameters);
    }

}