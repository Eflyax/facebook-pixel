<?php

namespace Tests\Functional;

use Eflyax\FacebookPixel\FacebookPixelService;
use Eflyax\FacebookPixel\IFacebookPixelFactory;

class DependencyInjectionTest extends BasePresenterTest
{

    public function testFacebookPixelService()
    {
        $fbPixelService = $this->tester->grabService(FacebookPixelService::class);
        $this->tester->assertInstanceOf(FacebookPixelService::class, $fbPixelService);
    }

    public function testFacebookPixelFactory()
    {
        $fbPixelFactory = $this->tester->grabService(IFacebookPixelFactory::class);
        $this->tester->assertInstanceOf(IFacebookPixelFactory::class, $fbPixelFactory);
    }

}
