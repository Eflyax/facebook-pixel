<?php declare(strict_types=1);

namespace Eflyax\FacebookPixel\Test;

use Eflyax\FacebookPixel\FacebookPixel;
use Eflyax\FacebookPixel\FacebookPixelService;
use Eflyax\FacebookPixel\IFacebookPixelFactory;
use Nette\Configurator;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class FacebookPixelServiceTest extends TestCase
{

    /** @var Container */
    private $container;

    public function setUp()
    {
        $this->container = $this->createContainer(__DIR__ . '/../tests.neon');
    }

    public function testDefaultServices()
    {
        Assert::type(FacebookPixelService::class, $this->container->getByType(FacebookPixelService::class));
        Assert::type(IFacebookPixelFactory::class, $this->container->getByType(IFacebookPixelFactory::class));
    }

    public function testFactory()
    {
        $factory = $this->container->getByType(IFacebookPixelFactory::class);
        Assert::type(FacebookPixel::class, $factory->create());
    }

    private function createContainer($config = null)
    {
        $configurator = new Configurator();
        $configurator->setTempDirectory(__DIR__ . '/temp');
        if ($config) {
            $configurator->addConfig($config);
        }

        return $configurator->createContainer();
    }

}

(new FacebookPixelServiceTest())->run();