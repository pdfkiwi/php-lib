## 0.3.0 (unreleased)
- Open sourced the project on Github.
- Removed Gitlab CI.
- Added coveralls coverage report.
- Translated `CONTRIBUTING.md` in english.
- Updated the composer.json with projet informations.
- Removed the deprecated `CURLOPT_DNS_USE_GLOBAL_CACHE` option.
- Removed the support for PHP < 5.6.

## 0.2.1 (2017-05-31)
- Fix: Update the `Pdfkiwi::$libVersion` version to `0.2.1`.

## 0.2.0 (2017-05-31)
- Removed the ability to set the API's URL (now hard-coded in the library).
- Added dev-dependencies: `phpcs` and `phpunit`.
- English translation of the `Pdfkiwi` class inline documentation.
- English translation of README.md and CHANGELOG.md files.
- Renvoi d'une erreur quand le code HTTP de la réponse est inférieur à 200 ou
  supérieur ou égal à 300, non plus seulement différent de 200 (#12).

## 0.1.7 (2017-05-16)
- Deleted a duplicate file (`PdfKiwiException`).
- Updated README with the new API address of `pdf.kiwi/php-lib`.
- Changelog normalizing: added release dates.

## 0.1.6 (2017-04-07)
- Added support of `header_footer_exclude_pages` option (to exclude footer and
  header from being rendered on some pages).

## 0.1.5 (2017-04-06)
- Added support of header and footer's spacing options: `header_spacing` and 
  `footer_spacing`.

## 0.1.4 (2017-03-28)
- Using SSL for default API endpoint address (https://pdf.kiwi).

## 0.1.3 (2017-03-03)
- Added methods `setHeaderText()` and `setFooterText()`.

## 0.1.2 (2017-02-24)
- Errors returned by API are better managed.

## 0.1.1 (2017-02-23)
- Fixed autoload bug: moving files in src/ (after verifications).

## 0.1.0 (2017-02-23)
- Creation of the PHP library to use pdf.kiwi API, with basic options.
