# Gendarmerie reduction website

This website is the official reduction website for all Gendarmerie employees.
<br>This one is composed of few entities allowing to post some reductions, comments and search them by filters.
<br>User auth is based on Symfony form login authentication.

Webpack is not intentionally implanted in this Github repository.

## Requirements

  * Php ^7.1.3      http://php.net/manual/fr/install.php;
  * Composer        https://getcomposer.org/download/;
  * SQL ^5.7        https://www.mysql.com/fr/downloads/;
  * and the [usual Symfony application requirements][1].

## Installation

```bash
$ make                          # Self documented Makefile
$ make install                  # Install all Symfony dependencies
$ make sf-console:server:run    # Start Symfony web server
$ make tests                    # Start PHPUnit tests and code coverage
```

> Take a look on Makefile rules to know which commands to use.

## Others

For [translation][2] to XLIFF files (`app_locales: en|fr`) :
```bash
$ make sf-console:translation:update --output-format xlf --dump-messages --force en
$ make sf-console:translation:update --output-format xlf --dump-messages --force fr
```
You can use [Loco][3] to manage all your translations. 5 domains are present : exceptions, flashes, forms, messages and validators.
<br>Two _locales: fr|en, fallbacks: en.

### Personal commands

To use a personal sample [command][4] (displaying all users from DB) :

```bash
$ make sf-console:app:list-users --help
$ make sf-console:app:list-users
```

Do not hesitate to create other commands useful for this project.

This project is based on symfony-website-skeleton-starter : [https://github.com/GaetanRole/symfony-website-skeleton-starter].

## Contributing

Do not hesitate to improve this repository, creating your PR on GitHub with a description which explains it.

Ask your question on `gaetan.role-dubruille@sensiolabs.com`.

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://symfony.com/doc/current/translation.html
[3]: https://localise.biz/
[4]: https://symfony.com/doc/current/console.html
