# Registration

```yaml
 registration:
    enabled: true
    route: /api/register
    form_type: Fourxxi\ApiUserBundle\Form\Type\UserRegistrationType
```

Registration uses Symfony Form component for submitting data and entity creation

By default form have `username` and `password` field with unique constraint on username field.

## Events
`RegistrationFormValidationFailedEvent` - form failed validation response event

`RegistrationResponseCompletedEvent` - success registration response event

`RegistrationUserPrePersistEvent` - this event triggers before user persisting (for example to add confirmation token)

`RegistrationUserCompletedEvent` - this event triggers after successful flushing user in database

