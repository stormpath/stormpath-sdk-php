<?php namespace Stormpath\Tests;



use Stormpath\Util\UserAgentBuilder;

class UserAgentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @expectedException \Stormpath\Exceptions\UserAgentException
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

        $userAgent = $userAgent->setSdkName('sdkName')
                                ->setOsName('osName')
                                ->setOsVersion('osVersion')
                                ->setPhpVersion('phpVersion')
                                ->setSdkVersion('sdkVersion')
                                ->build();

        $this->assertEquals(
            'sdkName/sdkVersion php/phpVersion osName/osVersion',
            $userAgent
        );


    }
}
