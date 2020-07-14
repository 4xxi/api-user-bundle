# Registration confirmation

```yaml
 registration:
    enabled: true
    confirmation:
      enabled: true
        route: /api/confirmation/{token}
        frontend_route: /confirmation/{token}
        credentials_generator: api_user.base_token_credentials_generator
```

Confirmation flow requires for some additional configuration:
1. Configure frontend base url
```yaml
  frontend:
    base_url: http://localhost
```
2. Configure message sender
```yaml
message_sender:
    from:
      email: test@mail.com
      name: test
    #service: api_user.message_sender
```
By default bundle service uses Symfony Mailer component and send `text/plain` emails. You can write own service and replace `service` key by own service id.

3. Configure User entity

3.1 Implement `ConfirmableUserInterface, EmailUserInterface` interfaces.

3.2 For fill `ConfirmableUserInterface` you can use `ConfirmableUserTrait`

## Translations
Bundle uses translation component for rendering messages. It can be overrided, check symfony documentation about translations overriding.

## Configuration
`route` -> backend route for confirming users. Important notice: `{route}` placeholder must be inserted in route because bundle use it.

`frontend_route` -> used in message for building confirmation url for end user. 

`credentials_generator` -> service which generates confirmation token hash credential.