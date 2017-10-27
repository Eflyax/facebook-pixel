<?php

namespace Eflyax\Facebook-pixel\DI;

use Eflyax\Facebook-pixel\FacebookPixel;
use Eflyax\Facebook-pixel\FacebookPixelService;
use Eflyax\Facebook-pixel\IFacebookPixelFactory;
use Nette;

/**
 * Class FacebookPixelExtension
 * @package Eflyax\Facebook-pixel\DI
 * Main of extension
 */
class FacebookPixelExtension extends Nette\DI\CompilerExtension
{

    public $defaults = [
        'id' => '<PIXEL_ID>',
    ];

    /**
     * Loads configuration from neon
     */
    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('facebookPixel'))
            ->setClass(FacebookPixel::class)
            ->setImplement(IFacebookPixelFactory::class)
            ->setArguments(['id' => $config['id']]);


        $builder->addDefinition($this->prefix('facebookPixelService'))
            ->setClass(FacebookPixelService::class);
    }

}
