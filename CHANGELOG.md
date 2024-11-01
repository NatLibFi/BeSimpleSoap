# Changelog

Notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 3.0.0 - 2024-11-01

**N.B. This version bumps the minimum PHP version to 8.1**

Anything marked with [**BC**] is known to affect backward compatibility with previous versions.

### Changed

- Dependencies have been updated, and laminas-mime has been replaced by zbateson/mail-mime-parser due to laminas-mime requiring the now-abandoned laminas-mail package for some operations.
- PHP_CodeSniffer, PHP CS Fixer and PHPStan have been added to the QA pipeline. This includes extensive harmonization of code style as well as fixing of several uncovered issues.
- PHP 8.3 is now properly supported.
- [**BC**] Several class names have been changed to start with uppercase letter, to not collide with PHP's built in names and/or to be autoloader compatible (e.g. float => FloatType in src/BeSimple/SoapCommon/Type/KeyValue/FloatType.php).

### Removed

- [**BC**] The Symfony bundle (SoapBundle) is no longer included. If you need it, the last version is available in the [dev-soap-bundle](https://github.com/NatLibFi/BeSimpleSoap/tree/dev-soap-bundle) branch.
