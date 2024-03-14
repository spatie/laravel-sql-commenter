# Changelog

All notable changes to `laravel-sql-commenter` will be documented in this file.

## 2.0.0 - 2024-03-14

### Upgrade guide

You can no longer call `SqlCommenter::addComment` statically, you now need to fetch the commenter instance from the container or have it dependency injected into your code.

```diff
- SqlCommenter::addComment('My comment');
+ app(SqlCommenter::class)->addComment('My comment');

```
### What's Changed

* Bump spatie/invade version to 2.0 by @vigneshgurusamy in https://github.com/spatie/laravel-sql-commenter/pull/19
* Add support for laravel 11 by @shuvroroy in https://github.com/spatie/laravel-sql-commenter/pull/20
* Add Octane compatibility by @riasvdv in https://github.com/spatie/laravel-sql-commenter/pull/22

### New Contributors

* @shuvroroy made their first contribution in https://github.com/spatie/laravel-sql-commenter/pull/20
* @riasvdv made their first contribution in https://github.com/spatie/laravel-sql-commenter/pull/22

**Full Changelog**: https://github.com/spatie/laravel-sql-commenter/compare/1.4.0...2.0.0

## 1.4.0 - 2023-07-11

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/spatie/laravel-sql-commenter/pull/12
- Bump dependabot/fetch-metadata from 1.3.6 to 1.4.0 by @dependabot in https://github.com/spatie/laravel-sql-commenter/pull/14
- Bump dependabot/fetch-metadata from 1.4.0 to 1.5.1 by @dependabot in https://github.com/spatie/laravel-sql-commenter/pull/15
- Bump dependabot/fetch-metadata from 1.5.1 to 1.6.0 by @dependabot in https://github.com/spatie/laravel-sql-commenter/pull/16
- Add support for commenting multiple database connections by @alexthekiwi in https://github.com/spatie/laravel-sql-commenter/pull/13

### New Contributors

- @alexthekiwi made their first contribution in https://github.com/spatie/laravel-sql-commenter/pull/13

**Full Changelog**: https://github.com/spatie/laravel-sql-commenter/compare/1.3.1...1.4.0

## 1.3.1 - 2023-01-26

- support L10

## 1.3.0 - 2023-01-16

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.4 to 1.3.5 by @dependabot in https://github.com/spatie/laravel-sql-commenter/pull/8
- Added ability to use relative path in FileCommenter by @peresmishnyk in https://github.com/spatie/laravel-sql-commenter/pull/11

### New Contributors

- @peresmishnyk made their first contribution in https://github.com/spatie/laravel-sql-commenter/pull/11

**Full Changelog**: https://github.com/spatie/laravel-sql-commenter/compare/1.2.0...1.3.0

## 1.2.0 - 2022-10-14

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.3 to 1.3.4 by @dependabot in https://github.com/spatie/laravel-sql-commenter/pull/6
- Add exclude path segment property for FileCommenter by @vigneshgurusamy in https://github.com/spatie/laravel-sql-commenter/pull/7

### New Contributors

- @vigneshgurusamy made their first contribution in https://github.com/spatie/laravel-sql-commenter/pull/7

**Full Changelog**: https://github.com/spatie/laravel-sql-commenter/compare/1.1.1...1.2.0

## 1.1.1 - 2022-07-04

- prevent infinite loop when getting authenticated user

## 1.1.0 - 2022-07-04

- add user commenter
- fix config:cache issue
- filter empty comments

## 1.0.0 - 2022-07-02

- initial release

## 0.0.3 - 2022-07-02

- experimental release

## 0.0.2 - 2022-07-02

- experimental release

## 0.0.1 - 2022-07-02

- experimental release
