# Getting started

1. Install bundle via composer.

2. Place this example configuration at `config/packages/api_user_bundle.yaml`
```yaml
api_user:
  user_class: App\Entity\User
```

Don't forget to create your user which implements Symfony `UserInterface`

3. Setup `config/packages/security.yaml`

3.1 Add security provider and user encoder
```yaml
providers:
    database_users:
        entity:
            class: 'App\Entity\User'
            property: 'username'

encoders:
    App\Entity\User:
        algorithm: auto
```

3.2 Add firewalls
```yaml
firewalls:
    public:
        pattern: ^/api/(register|confirmation)
        security: false

    api:
        stateless: true
        pattern: ^/api
        user_checker: api_user.user_checker
        guard:
            authenticators:
                - 'api_user.token_authenticator'
                - 'api_user.json_login_authenticator'
            entry_point: 'api_user.json_login_authenticator'
```

3.3 Modify access control
```yaml
access_control:
    - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/api, roles: ROLE_USER }
```

4. Add bundle router (`config/routes.yaml`)
```yaml
api_user:
  resource: .
  type: api_user
```

5. Make and run migrations
`bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate`

Basic configuration complete. As result, you have:
1. Token entity from bundle
2. /api/login endpoint
3. /api endpoint secured by token authenticator (X-API-TOKEN header with plain token)