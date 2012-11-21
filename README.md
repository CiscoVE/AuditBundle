Cisco Systems AuditBundle
=========================

Symfony 2 bundle for auditing processes, employees, etc.

## Features

This bundle offers the use of form (as in spreadsheet) for a user to audit a 
process and the management of these forms. Each form is divided in sections and
fields and offers four choices of answer possible. Each of the elements can be
modified at will through the administration part. (`No user management is 
included in this bundle`).

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

```
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

1. for now the following need to be added to the composer.json file of the project:

    "repositories": [
        { "type": "vcs", "url": "http://github.com/WrittenGames/AuditBundle" }
    ]

and

    "require": {
        "cisco-systems/audit-bundle": "dev-master"
    }

2. Then update through composer.phar

3. And add the bundle in the AppKernel:

        $bundles = array(
            new CiscoSystems\AuditBundle\CiscoSystemsAuditBundle(),
        );

## Issues

Issues should be reported in https://github.com/WrittenGames/AuditBundle/issues

## License

This bundle is under the BSD license: The license can be read in LICENSE.