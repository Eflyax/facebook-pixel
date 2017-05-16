<?php

namespace IncolorExtensions\FacebookRemarketing;

use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class FacebookPixelService
 * @package IncolorExtensions\FacebookRemarketing
 * Service for handling events through session
 */
class FacebookPixelService
{

    const EVENT_VIEW_CONTENT = 'ViewContent',
        EVENT_PURCHASE = 'Purchase',
        EVENT_ADD_TO_CART = 'AddToCart';

    /** @var SessionSection */
    private $sessionSection;

    /**
     * FacebookPixelService constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->sessionSection = $session->getSection(FacebookPixel::class);
    }

    /**
     * Activates given event
     * @param String $eventName
     */
    public function eventStart($eventName)
    {
        $this->sessionSection->$eventName = true;
    }

    /**
     * Deactivates given event
     * @param String $eventName
     */
    public function eventEnd($eventName)
    {
        unset($this->sessionSection->$eventName);
    }

    /**
     * Gets status of given event
     * @param String $eventName
     * @return String
     */
    public function eventStatus($eventName)
    {
        return $this->sessionSection->$eventName;
    }

}