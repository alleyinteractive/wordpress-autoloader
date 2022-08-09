# Changelog

All notable changes to `alleyinteractive/wordpress-autoloader` will be
documented in this file.

## [Unreleased]

- Adding APCu caching of autoloaded classes.
- Adds check to prevent multiple failed calls to autoload a class.

## [0.2.0]

- Supporting PHP 8.1
- Removing `preg_replace` with `str_*` functions.

## 0.1.2

- Small performance improvement.

## 0.1.1

- Ensure autoloader root path always has a trailing slash.

## 0.1.0

- Initial release.
