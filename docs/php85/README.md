# PHP 8.5 validation — 21 October 2026

The installed 1.0.2 package was reviewed separately after the application tests.
Its test file could not run as shipped: a use statement preceded the namespace,
and its PHPUnit 4 API was incompatible with the current test tooling. With a
mechanical adapter, 16 of 17 original tests passed on both 8.5.5 and 8.5.11;
the LIKE escaping test failed identically on both versions. This was a functional
regression already present in 1.0.2, not specific to the PHP patch release.
Passing null to escape/escapeLike also triggered str_replace deprecations.

The new suite retains the original tests, modernizes only their PHPUnit API,
and adds regression coverage. On the corrected source all **84 tests / 145
assertions pass on PHP 8.5.5 and PHP 8.5.11** with E_ALL enabled. Runtime warnings
are converted to exceptions; PHPUnit also fails on warnings, notices and
deprecations. Composer validation/platform requirements and PHP syntax checks
are performed on the installed checkout. Logs are stored alongside this report.

Twelve generated queries were parsed by the actual NIVO Java parser compiled
from the local API source. They verify raw wildcards, literal `_`/`-`, literal
dots, combinations, Unicode, dotted keys and operator-like value text. Source
hashes and expected parsed expressions are in backend-roundtrip.json. The first
probe showed that 1.0.2 and the old LIKE formatter lost a literal dot (`a.b`
became `ab`); the corrected formatter escapes dots across both parser layers.
No Java source was modified. This validates parser semantics, not database
matching or the deployed API version.

The optional tools/php85-app-filter-check.py check uses this package checkout
and the actual NIVO Filter Controller through PHP CGI, with six synthetic input
cases. It makes no API requests and does not edit the application or vendor tree.
It tests wildcards, dots, combined filters, field-name mapping, ignored input
and group filters. The earlier complete application staging tests used the
published 1.0.2 package; they are not claimed as tests of this unpublished update.

Reproduce package tests with composer install and composer test, with the intended
PHP runtime first in PATH. PHP 8.5.11 was separately built from the verified
official source in /private/tmp/neat-php-8.5.11; Homebrew PHP 8.5.5 is unchanged.
The Java parser probe is additional local evidence; Java is not required by the
package or by its PHPUnit suite.

The old Travis setup is unused, as confirmed by the user, and remains untouched.
Obsolete phpDocumentor/Coveralls development dependencies are removed from the
active Composer test setup; the legacy documentation deployment was not executed.

The runtime public methods remain untyped. Null still becomes an empty string;
this patch does not introduce .z semantics or validation of malformed expressions.
The LIKE corrections restore the original test contract and correct literal dots;
consumers relying on the automatic escaping in 1.0.2 need explicit escapeLike.
See CHANGELOG.md and the root README for examples.

Nothing has been committed, pushed, tagged or published. The NIVO application
still resolves 1.0.2 from its lockfile. Publish a new package release before
updating and revalidating that dependency in the application.

## Final installed-checkout verification

After applying the changes and installing the exact new lockfile in this repo,
both PHPUnit runs passed again: final-php855.txt and final-php8511.txt.
The six CGI filter cases passed again (final-app-filters.json), Composer validate
and platform checks passed, and all five source/test/example PHP files passed
syntax checks on 8.5.11. final-checks.json records the final source and lock hashes.

rollback.json records the temporary backup of replaced files. It does not contain
a backup of the old vendor directory; dependency rollback requires reinstalling
the restored lockfile with a runtime appropriate for those old development tools.
