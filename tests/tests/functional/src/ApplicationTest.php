<?php

namespace Tests\Functional;

use Arachne\Codeception\Module\NetteApplicationModule;
use Arachne\Codeception\Module\NetteDIModule;
use Codeception\Test\Unit;
use Nette\Application\Application;

/**
 * @author Jáchym Toušek <enumag@gmail.com>
 */
class ApplicationTest extends Unit
{

    /**
     * @var NetteApplicationModule|NetteDIModule
     */
    protected $tester;

    public function testApplication()
    {
        $this->assertInstanceOf(Application::class, $this->tester->grabService(Application::class));
    }

}
