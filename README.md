# sf4.4-official-gendarmerie-reduction-website

This website is the official reduction website for all Gendarmerie employees, called [Promogend](https://www.promogend.fr/fr/)
<br>This one is composed of few entities allowing to post some reductions, comments and search them by filters.
<br>This repository also shows how to develop applications following the Symfony Best Practices, according to my past experience in SensioLabs.

**Webpack is not intentionally implanted in this Github repository.**

## Requirements

  - [PHP ^7.3](http://php.net/manual/fr/install.php)
  - [SQL ^8](https://www.mysql.com/fr/downloads/)
  - [Symfony CLI](https://symfony.com/download)
  - [Composer](https://getcomposer.org/download)
  - and the [usual Symfony application requirements][1].

## Project view

![Login Page](project_readme_screenshot.png?raw=true "Login page")

## Installation

1 . Clone the current repository.
```bash
$ git clone 'https://github.com/GaetanRole/sf4.4-official-gendarmerie-reduction'
```

2 . Move in and create one global `.env.local` or few `.env.{environment}.local` files according to your environments with your default configuration.
**This one is not committed to the shared repository.**
> `.env` equals to the last `.env.dist` file before [november 2018][2].

3 . Set your DATABASE_URL and call the Makefile's install rule :

```bash
$ make              # Self documented Makefile
$ make install      # Install all Symfony dependencies
$ make tests        # Start PHPUnit tests and code coverage
```

> The project's Makefile has few rules which could be very useful.
> In fact, you have some rules for Q&A tools and unit/functional tests.
> Take a look on it !

## Usage

```bash
$ symfony serve --no-tls
```

> The web server bundle is no longer used anymore. Use the Symfony [binary][3] now.

### Translations

For [translation][4] to XLIFF files (`app_locales: en|fr`) :
```bash
$ make sf-console:translation:update ARGS='--output-format xlf --dump-messages --force en'
$ make sf-console:translation:update ARGS='--output-format xlf --dump-messages --force fr'
```

You can use [Loco][5] to manage all your translations. 6 domains are present : exceptions, flashes, forms, messages, security and validators.
<br>Two _locales: fr|en, fallbacks: en.

### Personal commands

To use a personal [command][6] (displaying all users from DB) :

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
[2]: https://symfony.com/doc/current/configuration.html#managing-multiple-env-files
[3]: https://symfony.com/download
[4]: https://symfony.com/doc/current/translation.html
[5]: https://localise.biz/
[6]: https://symfony.com/doc/current/console.html
