<?php

namespace Dynamic\BaseObject\Model;

use Exception;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Control\Director;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\LinkField\Form\LinkField;
use DNADesign\Elemental\Forms\TextCheckboxGroupField;

/**
 * Class BaseElementObject.
 *
 * @property string $Title
 * @property boolean $ShowTitle
 * @property string $Content
 *
 * @property int $ImageID
 * @property int $ElementLinkID
 *
 * @method Image Image()
 * @method Link ElementLink()
 *
 * @mixin Versioned
 */
class BaseElementObject extends DataObject
{
    /**
     * @var array
     */
    private static array $db = [
        'Title' => 'Varchar(255)',
        'ShowTitle' => 'Boolean',
        'Content' => 'HTMLText',
    ];

    /**
     * @var array
     */
    private static array $has_one = [
        'Image' => Image::class,
        'ElementLink' => Link::class,
    ];

    /**
     * @var array
     */
    private static array $owns = [
        'Image',
        'ElementLink',
    ];

    /**
     * @var string
     */
    private static string $default_sort = 'Title ASC';

    /**
     * @var array
     */
    private static array $summary_fields = [
        'Image.CMSThumbnail',
        'Title',
    ];

    /**
     * @var array
     */
    private static array $searchable_fields = [
        'Title',
        'Content',
    ];

    /**
     * @var array
     */
    private static array $extensions = [
        Versioned::class,
    ];

    /**
     * Adds Publish button.
     *
     * @var bool
     */
    private static bool $versioned_gridfield_extensions = true;

    /**
     * @var string
     */
    private static string $table_name = 'BaseElementObject';

    /**
     * @param bool $includerelations
     * @return array
     */
    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels($includerelations);

        $labels['Title'] = _t(__CLASS__ . '.TitleLabel', 'Title');
        $labels['ElementLink'] = _t(__CLASS__ . '.LinkLabel', 'Link');
        $labels['Image'] = _t(__CLASS__ . '.ImageLabel', 'Image');
        $labels['Image.CMSThumbnail'] = _t(__CLASS__ . '.ImageLabel', 'Image');
        $labels['Content'] = _t(__CLASS__ . '.ContentLabel', 'Content');

        return $labels;
    }

    /**
     * @return FieldList
     *
     * @throws Exception
     */
    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /** @var FieldList $fields */
            $fields->removeByName([
                'ElementFeaturesID',
                'Sort',
            ]);

            // Add a combined field for "Title" and "Displayed" checkbox in a Bootstrap input group
            $fields->removeByName('ShowTitle');
            $fields->replaceField(
                'Title',
                TextCheckboxGroupField::create()
                    ->setName('Title')
                    ->setTitle($this->fieldLabel('Title'))
            );

            $fields->replaceField(
                'ElementLinkID',
                LinkField::create('ElementLink', $this->fieldLabel('ElementLink'), $this)
                    ->setDescription(_t(__CLASS__ . '.LinkDescription', 'optional. Add a call to action link.'))
            );
            $fields->insertBefore('Content', $fields->dataFieldByName('ElementLink'));

            $image = $fields->dataFieldByName('Image')
                ->setDescription(_t(__CLASS__ . '.ImageDescription', 'optional. Display an image.'))
                ->setFolderName('Uploads/Elements/Objects');
            $fields->insertBefore('Content', $image);

            $fields->dataFieldByName('Content')
                ->setRows(8);
        });

        return parent::getCMSFields();
    }

    /**
     * @return SiteTree|null
     */
    public function getPage(): SiteTree|null
    {
        $page = Director::get_current_page();
        // because $page can be a SiteTree or Controller
        return $page instanceof SiteTree ? $page : null;
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param Member|null $member
     * @return bool
     */
    public function canView($member = null): bool
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        if ($page = $this->getPage()) {
            return $page->canView($member);
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param Member|null $member
     *
     * @return bool
     */
    public function canEdit($member = null): bool
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        if ($page = $this->getPage()) {
            return $page->canEdit($member);
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * Uses archive not delete so that current stage is respected i.e if a
     * element is not published, then it can be deleted by someone who doesn't
     * have publishing permissions.
     *
     * @param Member|null $member
     *
     * @return bool
     */
    public function canDelete($member = null): bool
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        if ($page = $this->getPage()) {
            return $page->canDelete($member);
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param Member|null $member
     * @param array $context
     *
     * @return bool
     */
    public function canCreate($member = null, $context = []): bool
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }
}
