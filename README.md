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

In order to help you customize your template, the four twig extensions are available:

```twig
    {{ get_resultforsection() }}
    {{ get_weightforsection() }}
    {{ get_resultforaudit() }}
    {{ get_weightforaudit() }}
```

Currently the forms are fairly static: Each Section is attached to one and only one
Form and each Field is attached to one and only one Section.

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

Once this all done, generate the five tables needed:

```php
    php app/console doctrine:schema:update --dump-sql
```

Those are:

* cisco_audit__audit
* cisco_audit__form
* cisco_audit__section
* cisco_audit__field
* cisco_audit__score

## TODO

 * Allow a section to be assigned to more than one Form
 * Allow a Field to be assigned to more than one Section

## Issues

Issues should be reported in [GitHub Issues] (https://github.com/WrittenGames/AuditBundle/issues)

## License

This bundle is under the BSD license: The license can be read in [LICENSE] (https://github.com/WrittenGames/AuditBundle/blob/master/LICENSE).
