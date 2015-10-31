<?php


namespace Stormpath\Tests\Resource;


use Stormpath\Client;
use Stormpath\Resource\Organization;
use Stormpath\Resource\Tenant;
use Stormpath\Stormpath;
use Stormpath\Tests\BaseTest;

class OrganizationTest extends BaseTest
{

    private static $organization = null;

    public function createOrganization()
    {
        self::$organization = Organization::instantiate([
            'name' => makeUniqueName('OrganizationForTests'),
            'nameKey' => 'nk'.md5(uniqid()),
            'description' => 'Organization used for the tests in OrganizationTest for PHP SDK'
        ]);

        self::createResource(Organization::PATH, self::$organization);



    }

    /**
     * @test
     */
    public function it_can_create_an_organization()
    {
        $this->assertNull(self::$organization);
        $this->createOrganization();
        $this->assertInstanceOf('\Stormpath\Resource\Organization', self::$organization);
        $this->assertNotNull(self::$organization->href);
    }



    /**
     * @test
     */
    public function it_can_get_all_properties()
    {
        $this->assertNotNull(self::$organization->createdAt);
        $this->assertNotNull(self::$organization->description);
        $this->assertNotNull(self::$organization->description);
        $this->assertNotNull(self::$organization->href);
        $this->assertNotNull(self::$organization->modifiedAt);
        $this->assertNotNull(self::$organization->name);
        $this->assertNotNull(self::$organization->nameKey);
        $this->assertNotNull(self::$organization->status);
        $this->assertNotNull(self::$organization->tenant);
        $this->assertInstanceof('\Stormpath\Resource\Account', self::$organization->accounts);
    }

    /**
     * @test
     */
    public function it_can_get_the_organization_off_the_data_store()
    {
        $org = Client::getInstance()->dataStore->getResource(self::$organization->href, Stormpath::ORGANIZATION);
        $this->assertEquals(self::$organization, $org);
    }

    /**
     * @test
     */
    public function it_can_get_the_organization_from_organization_class()
    {
        $org = Organization::get(self::$organization->href);
        $this->assertEquals(self::$organization,$org);
    }

    /**
     * @test
     */
    public function it_can_change_the_status_of_the_organization()
    {
        $this->assertEquals(Stormpath::ENABLED, self::$organization->status);
        self::$organization->status = Stormpath::DISABLED;
        self::$organization->save();
        $org = Organization::get(self::$organization->href);
        $this->assertEquals(Stormpath::DISABLED, $org->status);
        self::$organization->status = Stormpath::ENABLED;
        self::$organization->save();
        $org = Organization::get(self::$organization->href);
        $this->assertEquals(Stormpath::ENABLED, $org->status);
    }

    /**
     * @test
     */
    public function it_can_change_values_of_itself()
    {
        self::$organization->name = makeUniqueName('Changed Name');
        self::$organization->nameKey = 'something'.md5(uniqid());
        self::$organization->description = 'Some Description for PHP tests';
        self::$organization->save();

        $org = Organization::get(self::$organization->href);
        
        $this->assertContains('Changed_Name', $org->name);
        $this->assertContains('something', $org->nameKey);
        $this->assertEquals('Some Description for PHP tests', $org->description);

    }

    /**
     * @test
     * @expectedException \Stormpath\Resource\ResourceError
     * @expectedExceptionMessage The requested resource does not exist.
     */
    public function it_can_delete_an_organization()
    {
        $href =self::$organization->href;
        self::$organization->delete();

        $org = Organization::get($href);
    }

    public static function tearDownAfterClass()
    {
        if(self::$organization)
            self::$organization->delete();
    }
}
