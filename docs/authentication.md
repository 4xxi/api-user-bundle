# General configuration

```yaml
api_user:
  user_class: App\Entity\User

  token:
    use_bundled: true
    lifetime: 86400
    credentials_generator: api_user.base_token_credentials_generator

  token_auth:
      header: 'x-api-token'
      check_query_string: false
      credentials_provider: api_user.provider.credentials_bearer
      token_provider: api_user.provider.token

```

## User class
`user_class` field is mandatory for bundle. Ensure your User class implements `UserInterface` provided by Symfony.

## Token
By default bundle provides own Token entity for handling API tokens issued in login flow.

### Using bundled tokens

At first, you need create migrations (`bin/console d:m:diff`).
In `token` section you can configure token lifetime (in seconds) and set service which responsible for token credentials generation (token string).
By default token credentials service generating random hex with 16 bytes length.

### Implementing own tokens
If you want implement tokens by yourself, you need implement two things: `Token` (`Fourxxi\ApiUserBundle\Model\TokenInterface`) and `TokenRepository` (`Fourxxi\ApiUserBundle\Provider\TokenProviderInterface`).

Next you should configure `token` and `token_auth` section in configuration yaml:
```yaml
token:
    use_bundled: false

token_auth:
    ...
    token_provider: 'App\Provider\CustomTokenProvider'
```

## Authentication
```yaml
  token_auth:
    header: 'x-api-token'
    check_query_string: false
    credentials_provider: api_user.provider.credentials_bearer
    token_provider: api_user.provider.token
```

`header` -> header name where token placed

`check_query_string` -> boolean enables searching token in query string (?token=...)

`credentials_provider` -> service id, implementing `CredentialsProviderInterface`.
General purpose of this service is extracting token from header (or/and query).

Bundle provides two implementations `api_user.provider.credentials_plain` and `api_user.provider.credentials_bearer`.

`token_provider` -> repository for tokens

## Events
You can change some authentication responses by events
1. `TokenAuthenticationFailedEvent` - if provided api token is expired or not exists
3. `TokenAuthenticationUnavailableEvent` - no token provided or token can't be found by authentication guard