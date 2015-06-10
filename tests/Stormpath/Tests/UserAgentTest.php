<?php namespace Stormpath\Tests;



use Stormpath\Util\UserAgentBuilder;
use Stormpath\Util\Version;

class UserAgentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_if_properties_are_not_set()
    {
        $userAgent = new UserAgentBuilder;

        $userAgent->build();
    }

    /**
     * @test
     */
    public function it_can_build_an_user_agent_correctly()
    {
        $userAgent = new UserAgentBuilder;

        $userAgent = $userAgent->setOsVersion('osVersion')
                                ->setPhpVersion('phpVersion')
                                ->setOsName('osName')
                                ->build();

        $this->assertEquals(
            'stormpath-sdk-php/'.Version::SDK_VERSION.' php/phpVersion osName/osVersion',
            $userAgent
        );


    }
}
