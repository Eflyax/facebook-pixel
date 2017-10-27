<?php

namespace Eflyax\FacebookPixel;

use Nette\Application\UI\Control;

class FacebookPixel extends Control
{

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
        $this->template->setFile(__DIR__ . '/templates/facebookPixel.latte');
        $this->template->render();
    }

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

    public function renderAddToCart(
        $contentIds,
        $contentName = null,
        $contentCategory = null,
        $value = null,
        $currency = null,
        $selector = null
    )
    {
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

    public function renderPurchase($value, $currency, $contentIds = null)
    {
        if (!$this->facebookPixelService->eventStatus(FacebookPixelService::EVENT_PURCHASE)) {

            return;
        }
        if ($value) {
            $parameters['value'] = $this->formatPrice($value);
        }
        if ($currency) {
            $parameters['currency'] = "'{$currency}'";
        }
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
        $this->template->parameters = json_encode($parameters);
        $this->template->event = FacebookPixelService::EVENT_PURCHASE;
        $this->template->render(__DIR__ . '/templates/commonEvent.latte');
        $this->facebookPixelService->eventEnd(FacebookPixelService::EVENT_PURCHASE);
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
        $parameters['currency'] = $currency ? "'{$currency}'" : null;

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

}