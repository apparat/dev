# apparat/dev

[![Build Status](https://secure.travis-ci.org/apparat/dev.svg)](https://travis-ci.org/apparat/dev)
[![Coverage Status](https://coveralls.io/repos/apparat/dev/badge.svg?branch=master&service=github)](https://coveralls.io/github/apparat/dev?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/apparat/dev/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/apparat/dev/?branch=master)
[![Code Climate](https://codeclimate.com/github/apparat/dev/badges/gpa.svg)](https://codeclimate.com/github/apparat/dev)
[![Documentation Status](https://readthedocs.org/projects/apparat-dev/badge/?version=latest)](http://apparat-dev.readthedocs.io/en/latest/?badge=latest)

Purpose of this module:

1. Dummy repository factory
2. Reference repository with representative object resources

## Documentation

Please find the [project documentation](doc/index.md) in the `doc` directory. I recommend [reading it](http://apparat-dev.readthedocs.io/) via *Read the Docs*.

## Installation

This library requires PHP 5.6 or later. I recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

## Dependencies

![Composer dependency graph](https://rawgit.com/apparat/dev/master/doc/dependencies.svg)

## Quality

To run the unit tests at the command line, issue `composer install` and then `phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`, and [PHPUnit](http://phpunit.de/manual/) to be available as `phpunit`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
