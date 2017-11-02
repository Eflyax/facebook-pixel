<?php

namespace Eflyax\FacebookPixel;

use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class FacebookPixelService
 * @package Eflyax\FacebookPixel
 * Service for handling events through session
 */
class FacebookPixelService
{

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
     * Save special events to session
     * @param String[] $eventName
     * @param String[] $eventContent
     */
    public function saveEvent($eventName, $eventContent)
    {
        $this->sessionSection->$eventName = serialize($eventContent);

    }

    /**
     * @param String $eventName
     * @return String
     */
    public function getEventContent($eventName)
    {
        return unserialize($this->sessionSection->$eventName);
    }

    /**
     * @param String $eventName
     */
    public function removeEvent($eventName)
    {
        $this->sessionSection->$eventName = null;
    }

}