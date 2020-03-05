# sf4.4-official-gendarmerie-reduction-website

This website is the official reduction website for all Gendarmerie employees.
<br>This one is composed of few entities allowing to post some reductions, comments and search them by filters.
<br>User auth is based on Symfony form login authentication.

**Webpack is not intentionally implanted in this Github repository.**

## Requirements

  - [PHP ^7.3](http://php.net/manual/fr/install.php)
  - [SQL ^5.7](https://www.mysql.com/fr/downloads/)
  - [Composer](https://getcomposer.org/download)
  - and the [usual Symfony application requirements][1].

## Project view

![Login Page](project_readme_screenshot.png?raw=true "Login page")

## Installation

1 . Clone the current repository.

2 . Move in and create few `.env.{environment}.local` files, according to your environments with your default configuration
or only one global `.env.local`. **This one is not committed to the shared repository.**

3 . Set your DATABASE_URL and call the Makefile's install rule :

```bash
$ make                          # Self documented Makefile
$ make install                  # Install all Symfony dependencies
$ make tests                    # Start PHPUnit tests and code coverage
```

> Take a look on Makefile rules to know which commands to use.

## Usage

```bash
$ symfony serve --no-tls
```

> The web server bundle is no longer used anymore. Use the Symfony [binary][2] now.

### Others

For [translation][3] to XLIFF files (`app_locales: en|fr`) :
```bash
$ make sf-console:translation:update ARGS='--output-format xlf --dump-messages --force en'
$ make sf-console:translation:update ARGS='--output-format xlf --dump-messages --force fr'
```
You can use [Loco][4] to manage all your translations. 6 domains are present : exceptions, flashes, forms, messages, security and validators.
<br>Two _locales: fr|en, fallbacks: en.

### Personal commands

To use a personal [command][5] (displaying all users from DB) :

```bash
$ make sf-console:app:list-users --help
$ make sf-console:app:list-users
```

Do not hesitate to create other commands useful for this project.

This project is based on sf4.4-website-skeleton-starter : [https://github.com/GaetanRole/sf4.4-website-skeleton-starter].

## Contributing

Do not hesitate to improve this repository, creating your PR on GitHub with a description which explains it.

Ask your question on `gaetan.role@gmail.com`.

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://symfony.com/doc/current/setup/symfony_server.html
[3]: https://symfony.com/doc/current/translation.html
[4]: https://localise.biz/
[5]: https://symfony.com/doc/current/console.html
