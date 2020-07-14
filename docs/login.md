# Login

```yaml
api_user:
    login:
      route: /api/login
      allow_unconfirmed_login: false

```

## Route
Path to login endpoint. Route registered automatically by bundle router.
Ensure route in `security.yaml` firewall and here is equals.

## Allow unconfirmed login
By default unconfirmed users can't login into application. But you can change this behaviour by setting `allow_unconfirmed_login` to true

## Events
You can change response of login endpoint by events
1. `LoginAuthenticationFailedEvent` - if provided credentials are wrong
2. `LoginAuthenticationSuccessEvent` - successful response with token
3. `LoginAuthenticationUnavailableEvent` - invalid data sent in login endpoint (not json payload {username: string, password: string})