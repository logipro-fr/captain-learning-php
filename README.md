# Captain Learning PHP

Client PHP for Captain Learning API

## Install

```bash
composer require logipro/captain-learning-php
```

## Usage

### 1. Initialisation

Disposer d'une clé API et d'un ID de clé API valides
Configurer l'URL de base pour se connecter à l'API Captain Learning
```php
$apiKeyId = "cl_apk_690b25988d814d2b";
$apiKey = "sk_7a5f1b27ad33f4b6455ce8f96ca90bf2";
$url = 'http:monsite.fr';
$tenantId = "mon_tenantId"
```

### 2. Faire une instance du client

```php
$client = new CaptainLearningClient(
    $apiKeyId,
    $apiKey,
    $url,
    $tenantId
);
```

### 3. Créer une formation

```php
$formation = new FormationCreateRequest(
    'Nom de la formation',
    'Id de la formation',
    );

$client->createFormation($formation);
```

## Contributing

### Install

```bash
git clone https://github.com/logipro-fr/captain-learning-php
cd captain-learning-php
./install --profile devlocal
```
### Requirements

* docker
* git

### Add a .env.local file

To install locally or on a development server, be careful with the following environment variables:
* DATA_PATH: path where data is stored; must be inside the project (default: ./data)
* DATA_PATH_STORE: path for backups; generally outside the project (default: ../data/chatbot-php)
* REMOVE_DATABASE_WHEN_INSTALL: remove database during install (default: false)
* BUILD_WHEN_INSTALL: build application during install (default: false)
* DOCKER_DEV: run development-specific containers (default: false)
* DOCKER_PHP_BUILT_IMAGE: application prebuilt Docker image
* OPTIONAL_VOLUME: mount a local volume for localhost development (default: empty)
* LOCALDEV_WORKING_DIR: working directory useful for development (default: undefined)
* URL_API: override the base URL used by internal API clients (default: empty)
* PULL_POLICY: policy for pulling the PHP built image on start (default: missing)

Typical local development .env.local:

```
DATA_PATH=./data
REMOVE_DATABASE_WHEN_INSTALL=true
BUILD_WHEN_INSTALL=true
DOCKER_DEV=true
OPTIONAL_VOLUME=.:/var/chatbot-php
LOCALDEV_WORKING_DIR=true
URL_API=http://nginx
PULL_POLICY=never
```

Typical server development .env.local:
```
DATA_PATH=../data
REMOVE_DATABASE_WHEN_INSTALL=true
DOCKER_DEV=true
DOCKER_PHP_BUILT_IMAGE= 
URL_API=https://dev.your-app.tld
```

For production and pre-production, a .env.local MUST NOT exist because default variables target the production environment.
However, this project includes a frontend that calls the API, so in pre-production you need a .env.local to override `URL_API`.

Typical pre-production .env.local:
```
URL_API=https://preprod.your-app.tld
```


## Tests

### Unit tests

```bash
bin/phpunit
```

We use Test-Driven Development (TDD) principles and good practices.

### Integration tests
Integration tests are all test categories other than unit tests.

```bash
bin/integration
```

### Acceptance tests
Acceptance tests are integration tests that verify the application against feature specifications.
Gherkin is the specification language; Behat is the PHP runner.

```bash
bin/behat
```


## Manual tests

```bash
./start
```
Then open 172.17.0.1:35080/ in your browser.

```bash
./stop
```

## Quality

Some indicators we aim for:

* phpcs PSR12
* phpstan level 10
* coverage >=100%
* infection MSI >=100%

Quick check:
```bash
./codecheck
```

Check coverage:
```bash
bin/phpunit --coverage-html var
```
Then open `var/index.html` in your browser.

Check infection:
```bash
bin/infection
```
Then open `var/infection.html` in your browser.
