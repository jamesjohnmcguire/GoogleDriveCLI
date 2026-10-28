# Repository Guidelines

## Project Structure & Module Organization

`SourceCode/GoogleDriveCommand.php` is the CLI entry point; `GoogleDrive.php` implements Drive operations, and `Log.php` and `LogLevel.php` provide logging. `SourceCode/Command.cmd` is the Windows launcher. `DevelopmentTools/` contains build and test scripts, `Support/` contains development notes, and `SourceCode/stubs/` supports PHPStan. Composer installs dependencies into the ignored `SourceCode/vendor/` directory. There is currently no top-level `Tests/` directory; add focused tests there when introducing testable behavior.

## Build, Test, and Development Commands

Run commands from the repository root:

- `composer install --prefer-dist` installs the locked dependencies.
- `composer validate --strict` checks `composer.json` and the lock file.
- `SourceCode/vendor/bin/phpstan analyse` runs static analysis at level 7, using `phpstan.neon`.
- `SourceCode/vendor/bin/phpcs -sp --standard=ruleset.xml SourceCode` checks production code style.
- `php SourceCode/GoogleDriveCommand.php list` runs the CLI against the configured Google Drive account; use valid service account credentials.

`DevelopmentTools/build.cmd` and `build.sh` combine dependency, lint, analysis, style, and PHPUnit checks, but some commands still point to root-level `vendor/` and the absent `Tests/` tree. Check those paths before relying on the full scripts.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, final newline, no trailing whitespace, and tabs with width four for PHP and shell files. Keep `declare(strict_types=1);` and the `DigitalZenWorks\GoogleDrive` namespace in PHP source. Match existing PascalCase class and method names such as `GoogleDrive` and `GetFile`; use descriptive camelCase variables. Run PHPCS with `ruleset.xml` before submitting changes.

## Testing Guidelines

PHPUnit is declared in `composer.json`, and the scripts expect `Tests/phpunit.xml`. When adding tests, create that configuration and name test files `*Test.php`; keep tests independent of a live Drive account where possible. Run them with `SourceCode/vendor/bin/phpunit -c Tests/phpunit.xml`. No coverage threshold is configured.

## Commit & Pull Request Guidelines

Recent commits use short, imperative, title-case subjects, such as `Add Type Hints` and `Update Packages`. Keep each commit focused. Pull requests should explain the behavior changed, list validation commands and results, and note any required account setup or linked issue. Include CLI output only when it helps reviewers verify user-visible behavior; redact IDs and credentials.

## Security & Configuration

The CLI uses Google service account authorization. Keep key files, tokens, and account-specific paths out of Git; never paste their contents into issues, logs, or test fixtures.