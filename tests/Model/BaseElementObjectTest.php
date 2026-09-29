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
use SilverStripe\LinkField\Models\ExternalLink;
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

        Director::set_current_page(null);
        $controller->popCurrent();
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

    /**
     * Regression test: duplicating an object must fork its ElementLink instead of
     * copying the ElementLinkID foreign key, so the copy and the original no longer
     * share one Link record.
     */
    public function testDuplicateForksElementLink()
    {
        /** @var ExternalLink $link */
        $link = ExternalLink::create();
        $link->ExternalUrl = 'https://example.com/original';
        $link->write();

        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);
        $object->ElementLinkID = $link->ID;
        $object->write();

        /** @var BaseElementObject $copy */
        $copy = $object->duplicate();

        $this->assertNotEquals(0, $copy->ElementLinkID, 'Duplicated object should have its own link');
        $this->assertNotEquals(
            $object->ElementLinkID,
            $copy->ElementLinkID,
            'Duplicated object must not share the original ElementLink record'
        );
        $this->assertEquals(
            'https://example.com/original',
            $copy->ElementLink()->ExternalUrl,
            'The duplicated link should keep the original URL'
        );
    }

    /**
     * Duplicating an object without a link must keep ElementLinkID at zero without error.
     */
    public function testDuplicateWithoutElementLink()
    {
        /** @var BaseElementObject $object */
        $object = Injector::inst()->create(BaseElementObject::class);
        $object->write();

        /** @var BaseElementObject $copy */
        $copy = $object->duplicate();

        $this->assertEquals(0, $copy->ElementLinkID);
    }
}
