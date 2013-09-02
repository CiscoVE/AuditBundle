Cisco Systems AuditBundle
=========================

[![Build Status](https://travis-ci.org/WrittenGames/AuditBundle.png?branch=master)](https://travis-ci.org/WrittenGames/AuditBundle)

Symfony 2 bundle for auditing processes, employees, etc.

## Features

This bundle offers the use of form (as in spreadsheet) for a user to audit a
process and the management of these forms. Each form is divided in sections and
fields and offers four choices of answer possible. Each of the elements can be
modified at will through the administration part. (`No user management is
included in this bundle`).

In order to help you customize your template, the following twig extensions are available:

### twig functions:
```twig
    {{ get_resultforsection() }}        // return the score for the section
    {{ get_weightforsection() }}        // return the weight for the section
    {{ get_resultforaudit() }}          // return the result for the audit
    {{ get_weightforaudit() }}          // return the weight for the audit
```
### twig filters:
```twig
    {{ element | archived ( parent ) }} // return the value of archived for the element and its parent element (section/form and field/section)
    {{ element | position ( parent ) }} // return the value of position for the element and its parent element (section/form and field/section)
    {{ form | sections( false|true ) }} // return the sections for the form archived === false | true
    {{ section | fields( false|true ) }}// return the fields for the section archived === false | true
```

~~Currently the forms are fairly static: Each Section is attached to one and only one
Form and each Field is attached to one and only one Section.~~

The above was correct in the previous iteration of the bundle. Currently, the schema
has been changed to allow a Section to be assigned to more than one Form and
similarily a Field can be assigned to more than one Section. Which means that
the relationship between Form and Section, and between Section and Field are of
Many to Many. However, in order to archive Sections, and Fields, the relationship
itself has been materialized in a class that holds the property $archived.

__PLEASE NOTE__: the position of Sections on Form and Fields on Section is currently
not working as the gedmo extension was to be used, but a change in the schema
broke this.

## Configuration

If audits are to be saved with a reference to the user having done the auditing
the class of the User entity should be configured. Otherwise the user field will
be set to NULL.

Likewise, if instead of a text input with the audit reference a dropdown menu
with entities should be rendered, the class of the audited entity needs to be
configured as well.

Also, a control user can be notified if an audit is considered as containing
fatal errors.

Below is a complete example configuration (config.yml):

```yaml
cisco_audit:
    control_user: true
    user:
        class: Acme\UserBundle\Entity\User
        property: id
    audit_reference:
        class: Acme\ServiceBundle\Entity\ServiceCase
        property: caseId
```

## Required

For now the following need to be added to the composer.json file of the project:

```yaml
    "repositories": [
        { "type": "vcs", "url": "http://github.com/WrittenGames/AuditBundle" }
    ]

    ...

    "require": {
        "cisco-systems/audit-bundle": "dev-master"
    }
```
Then update through composer.phar

And add the bundle in the AppKernel:

```php
    $bundles = array(
        new Craue\TwigExtensionsBundle\CraueTwigExtensionsBundle(),
        new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        new CiscoSystems\AuditBundle\CiscoSystemsAuditBundle(),
    );
```

Add as well the Bundle to the routing.yml:

```yaml
    CiscoSystemsAuditBundle:
        resource: "@CiscoSystemsAuditBundle/Resources/config/routing.yml"
        prefix:   /cisco_audit
```

Finally add the configuration for the stof bundle in the config.yml file:

```yaml
    # Doctrine Extensions
    stof_doctrine_extensions:
        orm:
            default:
                timestampable: true
                sluggable: true
                sortable: true
```

And the orm bundle for the user interface:

```yaml
    doctrine:
        orm:
            resolve_target_entities:
                CiscoSystems\AuditBundle\Model\UserInterface: Acme\UserBundle\Entity\User
                CiscoSystems\AuditBundle\Model\ReferenceInterface: Acme\UserBundle\Entity\Reference
                CiscoSystems\AuditBundle\Model\MetadataInterface: Acme\AuditBundle\Entity\Metadata
```

Once this all done, generate the tables needed:

```php
    php app/console doctrine:schema:update --dump-sql
```

Those are:

* audit__audit
* audit__score
* audit__element
* audit__form
* audit__section
* audit__field
* audit__relation
* audit__form_section
* audit__section_field

### inheritance and doctrine

Please note that the class Element is abstract and Form, Section and Field are
all children of that class. The same apply to the class Relation and its children:
FormSection and SectionField.

Please note as well, that ManyToMany relationship exist between Form and Section,
and Section and Field.

### command

if you have saved (somehow) some audit with a totalscore of 0 (zero), you can regenerate those
with the following command:

```
    php app/console audit:score:regenerate
```
 * Option: 'id' as the audit id to process.
 * Option: '--override' to regenerate all the total score and not just the one with value of 0.

## TODO

 * Allow a section to be assigned to more than one Form
 * Allow a Field to be assigned to more than one Section
 * Implement functional and unit testing
    * Entity done
    * maybe use [ICBaseTestBundle] (https://github.com/instaclick/ICBaseTestBundle)

## Issues

Issues should be reported in [GitHub Issues] (https://github.com/WrittenGames/AuditBundle/issues)

## License

This bundle is under the BSD license: The license can be read in [LICENSE] (https://github.com/WrittenGames/AuditBundle/blob/master/LICENSE).
