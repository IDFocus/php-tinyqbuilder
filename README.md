# Implementing the TinyQ expressions in PHP

[![Latest Stable Version](https://img.shields.io/packagist/v/idfocus/php-tinyqbuilder.svg?style=flat-square)](https://packagist.org/packages/idfocus/php-tinyqbuilder)
[![Total Downloads](https://img.shields.io/packagist/dt/idfocus/php-tinyqbuilder.svg?style=flat-square)](https://packagist.org/packages/idfocus/php-tinyqbuilder)
[![License](https://img.shields.io/packagist/l/idfocus/php-tinyqbuilder.svg?style=flat-square)](https://packagist.org/packages/idfocus/php-tinyqbuilder)

Class for making TinyQ expressions using PHP 

Find a reference [here](https://www.novell.com/developer/plugin-sdk/ref/restapi/7.0/javadoc/com/novell/sentinel/client/tinyq/TinyQBuilder.html)

# Documentation

Up-to-date documentation can be found [here](https://idfocus.github.io/php-tinyqbuilder/classes/IDFocus.TinyQ.Builder.html).


## PHP 8.5 compatibility

The builder has been tested on PHP 8.5.5 and 8.5.11 with E_ALL enabled.
The runtime API remains untyped. PHPUnit 12 is a development dependency;
installing and running this test suite requires PHP 8.3 or newer.

```sh
composer install --no-interaction --no-scripts --prefer-dist
composer test
```

The 84 tests cover comparison operators, chaining, grouping, reset, Unicode,
dots, wildcard escaping and legacy scalar/null conversion. Null values retain
the previous empty-string behavior without a PHP deprecation; this is not a
conversion to the TinyQ null operator `.z`.

### LIKE patterns

`_` matches one character and `-` matches multiple characters. Use `escapeLike()`
for literal underscore and dash characters; then append any intended wildcards:

```php
$builder = new \IDFocus\TinyQ\Builder();
$query = $builder->like('name', $builder->escapeLike('some_text') . '-')->build();
// name.ksome._text-: literal "some_text", followed by a wildcard.
```

This restores the explicit escaping contract covered by the original tests.
Version 1.0.2 automatically escaped LIKE values and could double-escape an
explicitly escaped value. Callers relying on that behavior should now use
`escapeLike()` for literal `_` and `-` characters. Literal dots in LIKE values
are escaped through both parser layers, so `a.b` reaches the backend as `a.b`.
Non-LIKE comparisons retain their existing escaping behavior.

An optional integration check exercises the real NIVO filter controller using
this checkout's builder, synthetic CGI input and no API requests:

```sh
python3 tools/php85-app-filter-check.py /path/to/nivo-neat-ui-php
```

Both `php` and `php-cgi` should resolve to the PHP runtime being validated.
Detailed results and scope are recorded in `docs/php85/README.md`.
The old Travis configuration is unused and was not part of this validation.
