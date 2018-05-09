<?php

namespace Dynamic\BaseObject\Tests;

use Dynamic\BaseObject\Model\BaseElementObject;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;

class BaseElementObjectTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = '../fixtures.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testValidateName()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);
        $valid = $object->validate()->isValid();
        $this->assertFalse($valid);

        $object->Name = 'Title';
        $valid = $object->validate()->isValid();
        $this->assertTrue($valid);
    }

    /**
     *
     */
    public function testGetPage()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);
        $this->assertNull($object->getPage());
        
        $request = new HTTPRequest('GET', '/');
        $session = new Session([]);
        $request->setSession($session);
        /** @var ContentController $controller */
        $controller = ContentController::create();
        $controller->setRequest($request);
        $controller->pushCurrent();
        $this->assertNull($object->getPage());

        /** @var SiteTree $page */
        $page = $this->objFromFixture(SiteTree::class, 'home');
        Director::set_current_page($page);
        $this->assertInstanceOf(SiteTree::class, $object->getPage());
    }

    /**
     *
     */
    public function testCanView()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canView($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canView($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canView($member));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canEdit($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canEdit($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canDelete($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canDelete($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);

        /** @var Member $admin */
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canCreate($admin));

        /** @var Member $siteowner */
        $siteowner = $this->objFromFixture(Member::class, 'site-owner');
        $this->assertTrue($object->canCreate($siteowner));

        /** @var Member $member */
        $member = $this->objFromFixture(Member::class, 'default');
        $this->assertFalse($object->canCreate($member));
    }
}
