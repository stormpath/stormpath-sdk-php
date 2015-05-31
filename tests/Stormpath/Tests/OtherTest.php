<?php namespace Stormpath\Tests;



use Stormpath\Util\UserAgent;

class OtherTest extends BaseTest {

    public function testResolveUserAgent()
    {
        $userAgent = new UserAgent;

        $userAgent->add('key1','value1')
                  ->add('key2','value2')
                  ->add('key3','value3');

        $this->assertEquals('key1/value1 key2/value2 key3/value3', $userAgent->getUserAgent());
    }
}
