<?php

namespace Tests\Functional;

use Arachne\Codeception\Module\NetteApplicationModule;
use Arachne\Codeception\Module\NetteDIModule;
use Codeception\Test\Unit;
use Nette\Application\LinkGenerator;
use Nette\Http\IResponse;

abstract class BasePresenterTest extends Unit
{

    const FB_PIXEL_ID = '111111111111111';

    /** @var LinkGenerator */
    protected $linkGenerator;

    public function _before()
    {
        $this->linkGenerator = $this->tester->grabService(LinkGenerator::class);
    }

    /**
     * @var NetteApplicationModule|NetteDIModule
     */
    protected $tester;

    protected function checkUrlAndResponse($url, $rewriteUrl = null, $response = IResponse::S200_OK)
    {
        $rewriteUrl = $rewriteUrl ?: $url;
        $this->tester->amOnPage($url);
        $this->tester->seeInCurrentUrl($rewriteUrl);
        $this->tester->seeResponseCodeIs($response);
    }

    public function generateLink($destination, $parameters = [])
    {
        return str_replace('http://', '', $this->linkGenerator->link($destination, $parameters));
    }

}
