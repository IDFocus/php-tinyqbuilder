# Changelog

## Unreleased

- Handle null search values and null escapeLike input without PHP deprecations,
  preserving the previous empty-string conversion.
- Restore the explicit escapeLike contract: raw `_` and `-` remain wildcards;
  escapeLike makes them literal without double escaping.
- Preserve literal dots through the scanner and LIKE pattern parser.
- Fix the test namespace, modernize PHPUnit and lock the development dependencies.
- Add PHP 8.5 regressions and an optional NIVO filter integration check.

The LIKE fixes change the incorrect output produced by 1.0.2. Review callers
that depended on automatic escaping before upgrading. No release is published
by these local changes.
