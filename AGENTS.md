# Repository Guidelines

## Project Structure & Module Organization

`SourceCode/GoogleDriveCommand.php` is the CLI entry point; `GoogleDrive.php` implements Drive operations, and `Log.php` and `LogLevel.php` provide logging. `SourceCode/Command.cmd` is the Windows launcher. `DevelopmentTools/` contains build and test scripts, `Support/` contains development notes, and `SourceCode/stubs/` supports PHPStan. Composer installs dependencies into the ignored `SourceCode/vendor/` directory. `Tests/` contains PHPUnit configuration and offline CLI tests.

## Build, Test, and Development Commands

Run commands from the repository root:

- `composer install --prefer-dist` installs the locked dependencies.
- `composer validate --strict` checks `composer.json` and the lock file.
- `SourceCode/vendor/bin/phpstan analyse` runs static analysis at level 7, using `phpstan.neon`.
- `SourceCode/vendor/bin/phpcs -sp --standard=ruleset.xml SourceCode` checks production code style.
- `php SourceCode/GoogleDriveCommand.php list` runs the CLI against the configured Google Drive account; use valid service account credentials.

`DevelopmentTools/build.cmd` and `build.sh` run dependency, syntax, analysis, style, and PHPUnit checks.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, final newline, no trailing whitespace, and tabs with width four for PHP and shell files. Keep `declare(strict_types=1);` and the `DigitalZenWorks\GoogleDrive` namespace in PHP source. Match existing PascalCase class and method names such as `GoogleDrive` and `GetFile`; use descriptive camelCase variables. Run PHPCS with `ruleset.xml` before submitting changes.

## Testing Guidelines

PHPUnit uses `Tests/phpunit.xml`. Name test files `*Test.php` and keep tests independent of a live Drive account where possible. Run them with `DevelopmentTools/UnitTests.cmd` or `bash DevelopmentTools/unittests.sh`. No coverage threshold is configured.

## Commit & Pull Request Guidelines

Recent commits use short, imperative, title-case subjects, such as `Add Type Hints` and `Update Packages`. Keep each commit focused. Pull requests should explain the behavior changed, list validation commands and results, and note any required account setup or linked issue. Include CLI output only when it helps reviewers verify user-visible behavior; redact IDs and credentials.

## Security & Configuration

The CLI uses Google service account authorization. Keep key files, tokens, and account-specific paths out of Git; never paste their contents into issues, logs, or test fixtures.