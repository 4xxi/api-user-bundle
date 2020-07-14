# Bundle configuration reference

```yaml
api_user:
  user_class: App\Entity\User

  frontend:
    base_url: http://localhost

  message_sender:
    from:
      email: test@mail.com
      name: test
    service: api_user.message_sender

  token:
    use_bundled: true
    lifetime: 86400
    credentials_generator: api_user.base_token_credentials_generator

  token_auth:
    header: 'x-api-token'
    check_query_string: false
    credentials_provider: api_user.provider.credentials_bearer
    token_provider: api_user.provider.token

  login:
    route: /api/login
    allow_unconfirmed_login: false

  registration:
    enabled: true
    route: /api/register
    form_type: Fourxxi\ApiUserBundle\Form\Type\UserRegistrationType
    confirmation:
      enabled: true
      route: /api/confirmation/{token}
      frontend_route: /confirmation/{token}
      credentials_generator: api_user.base_token_credentials_generator
```

Configuration reference explained by sections in documentation below.