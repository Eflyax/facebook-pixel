<?php

namespace IncolorExtensions\FacebookRemarketing;

/**
 * Interface IFacebookPixelFactory
 * @package IncolorExtensions\FacebookRemarketing
 * Interface for FacebookPixelFactory
 */
interface IFacebookPixelFactory
{

    /**
     * @return FacebookPixel
     */
    public function create();

}