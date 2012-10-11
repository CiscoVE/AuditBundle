Written Games AuditBundle
=========================

Symfony 2 bundle for auditing processes, employees etc.

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
wg_audit:
    control_user: true
    user:
        class: Acme\UserBundle\Entity\User
        property: id
    audit_reference:
        class: Acme\ServiceBundle\Entity\ServiceCase
        property: caseId
```
