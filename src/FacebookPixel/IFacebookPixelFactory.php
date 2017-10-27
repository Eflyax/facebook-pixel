<?php

namespace Eflyax\FacebookPixel;

/**
 * Interface IFacebookPixelFactory
 * @package Eflyax\FacebookPixel
 * Interface for FacebookPixelFactory
 */
interface IFacebookPixelFactory
{

    /**
     * @return FacebookPixel
     */
    public function create();

}