# Silverstripe Elemental Baseobject

a simple base dataobject to use with elements

[![CI](https://github.com/dynamic/silverstripe-elemental-baseobject/actions/workflows/ci.yml/badge.svg)](https://github.com/dynamic/silverstripe-elemental-baseobject/actions/workflows/ci.yml)

[![Latest Stable Version](https://poser.pugx.org/dynamic/silverstripe-elemental-baseobject/v/stable)](https://packagist.org/packages/dynamic/silverstripe-elemental-baseobject)
[![Total Downloads](https://poser.pugx.org/dynamic/silverstripe-elemental-baseobject/downloads)](https://packagist.org/packages/dynamic/silverstripe-elemental-baseobject)
[![Latest Unstable Version](https://poser.pugx.org/dynamic/silverstripe-elemental-baseobject/v/unstable)](https://packagist.org/packages/dynamic/silverstripe-elemental-baseobject)
[![License](https://poser.pugx.org/dynamic/silverstripe-elemental-baseobject/license)](https://packagist.org/packages/dynamic/silverstripe-elemental-baseobject)

## Requirements

* SilverStripe ^6.0
* dnadesign/silverstripe-elemental ^6.0
* silverstripe/linkfield ^5.0
* PHP ^8.1

## Installation

`composer require dynamic/silverstripe-elemental-baseobject`

## License

See [License](LICENSE.md)

## Upgrading from version 2

BaseObject drops `sheadawson/silverstripe-linkable` usage in favor of `gorriecoe/silverstripe-linkfield`. To avoid data loss, install the `dynamic/silverstripe-link-migrator` module as follows:

```markdown
composer require dynamic/silverstripe-link-migrator
```

Then, run the task "Linkable to SilverStripe Link Migration" via `/dev/tasks`, or cli via:
```markdown
vendor/bin/sake dev/tasks/LinkableMigrationTask
```

This will populate all of the new Link fields with data from the old class.

## Usage

`BaseElementObject` is a versioned DataObject that provides a reusable foundation for managing collections of related content within Elemental blocks. It's designed to be extended or used as a `has_many` relationship in custom Element classes.

### Features

The base object includes:

- **Title** - Text field with optional display toggle (using `TextCheckboxGroupField`)
- **Content** - HTML text area for rich content
- **Image** - Image upload with automatic organization into `Uploads/Elements/Objects`
- **Link** - Configurable call-to-action using SilverStripe LinkField
- **Versioning** - Full draft/publish workflow with GridField extensions
- **Permissions** - Inherits permissions from the current page context

### Common Usage Pattern

Typically used as a `has_many` relationship in Elemental blocks:

```php
use Dynamic\BaseObject\Model\BaseElementObject;
use DNADesign\Elemental\Models\BaseElement;

class ElementAccordion extends BaseElement
{
    private static $has_many = [
        'Items' => BaseElementObject::class,
    ];
}
```

### Extending BaseElementObject

For custom functionality, extend the class:

```php
use Dynamic\BaseObject\Model\BaseElementObject;

class PromoObject extends BaseElementObject
{
    private static $db = [
        'Subtitle' => 'Varchar(255)',
    ];
    
    private static $table_name = 'PromoObject';
}
```

### Used By

This module serves as a dependency for several Dynamic Elemental modules:

* [Accordion](https://github.com/dynamic/silverstripe-elemental-accordion) - Collapsible content panels
* [Features](https://github.com/dynamic/silverstripe-elemental-features) - Icon-based feature highlights
* [Gallery](https://github.com/dynamic/silverstripe-elemental-gallery) - Image galleries with captions
* [Promos](https://github.com/dynamic/silverstripe-elemental-promos) - Promotional content blocks
* [Sponsors](https://github.com/dynamic/silverstripe-elemental-sponsors) - Sponsor/partner logos
* [Timeline](https://github.com/dynamic/silverstripe-elemental-timeline) - Event timelines

## Getting more elements

See [Elemental modules by Dynamic](https://github.com/orgs/dynamic/repositories?q=elemental&type=all&language=&sort=)

## Configuration

See [SilverStripe Elemental Configuration](https://github.com/silverstripe/silverstripe-elemental#configuration)

## Translations

The translations for this project are managed via [Transifex](https://www.transifex.com/dynamicagency/silverstripe-elemental-baseobject/)
and are updated automatically during the release process. To contribute, please head to the link above and get
translating!

## Maintainers

 *  [Dynamic](https://www.dynamicagency.com) (<dev@dynamicagency.com>)

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
