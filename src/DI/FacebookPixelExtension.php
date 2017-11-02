<?php

namespace Eflyax\FacebookPixel\DI;

use Eflyax\FacebookPixel\FacebookPixel;
use Eflyax\FacebookPixel\FacebookPixelService;
use Eflyax\FacebookPixel\IFacebookPixelFactory;
use Nette;

/**
 * Class FacebookPixelExtension
 * @package Eflyax\FacebookPixel\DI
 * Main of extension
 */
class FacebookPixelExtension extends Nette\DI\CompilerExtension
{

    public $defaults = [
        'id' => '<PIXEL_ID>',
        'productIdPrefix' => '',
    ];

    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('facebookPixel'))
            ->setClass(FacebookPixel::class)
            ->setImplement(IFacebookPixelFactory::class)
            ->setArguments([
                'id' => $config['id'],
                'productIdPrefix' => $config['productIdPrefix'],
            ]);

        $builder->addDefinition($this->prefix('facebookPixelService'))
            ->setClass(FacebookPixelService::class);
    }

}
