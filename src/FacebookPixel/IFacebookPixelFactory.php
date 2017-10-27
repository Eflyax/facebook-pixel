<?php

namespace Eflyax\Facebook-pixel;

/**
 * Interface IFacebookPixelFactory
 * @package Eflyax\Facebook-pixel
 * Interface for FacebookPixelFactory
 */
interface IFacebookPixelFactory
{

    /**
     * @return FacebookPixel
     */
    public function create();

}