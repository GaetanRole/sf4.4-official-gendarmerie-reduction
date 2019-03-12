Gendarmerie reduction website
=============================

This website is the official reduction website for all Gendarmerie employees.
This one is composed of few entities allowing to post some reductions, comments and search them by filters.
User auth is based on Symfony form login authentication.

Webpack is not intentionally implanted in this Github repository. 

Requirements
------------

  * Php ^7.1.3    http://php.net/manual/fr/install.php;
  * Composer        https://getcomposer.org/download/;
  * and the [usual Symfony application requirements][1].

Installation
------------

1 . Clone the current repository.

2 . Move in and create few `.env.{environment}.local` files according to your environments with your default configuration
or only one global `.env.local`. **This one is not committed to the shared repository.**
 
> `.env` equals to the last `.env.dist` file before [november 2018][2].

3 .a. Execute commands below into your working folder to install the project :

```bash
$ composer install
$ composer update
$ bin/console d:d:c
$ bin/console d:m:m
$ bin/console d:m:s
```
3 .b. Or execute a simple shell script :

```bash
$ ./install.sh
```

Usage
-----

```bash
$ bin/console s:r
$ bin/console c:c --env=dev
```

For loading fixtures [fixture][3] :
```bash
$ bin/console d:f:l
```

For [translation][4] to XLIFF files (`app_locales: en|fr`) :
```bash
$ bin/console translation:update --output-format xlf --dump-messages --force en
$ bin/console translation:update --output-format xlf --dump-messages --force fr
```

Personal commands
-----------------
To use a personal sample [command][5] (displaying all users from DB) :

```bash
$ bin/console app:list-users --help
$ bin/console app:list-users
```

Do not hesitate to create other commands useful for this project.

This project is based on symfony4-starter-kit : [https://github.com/GaetanRole/symfony4-website-starter-kit].

[1]: https://symfony.com/doc/current/reference/requirements.html
[2]: https://symfony.com/doc/current/configuration.html#the-env-file-environment-variables
[3]: https://symfony.com/doc/current/doctrine.html#doctrine-fixtures
[4]: https://symfony.com/doc/current/translation.html
[5]: https://symfony.com/doc/current/console.html

08/03/2019 gaetan@wildcodeschool.fr
