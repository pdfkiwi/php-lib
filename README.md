# pdfkiwi/php-lib

> A PHP library for interacting with [pdf.kiwi](https://pdf.kiwi) API

[![Build Status](https://travis-ci.org/pdfkiwi/php-lib.svg?branch=master)](https://travis-ci.org/pdfkiwi/php-lib)
[![Coverage Status](https://coveralls.io/repos/github/pdfkiwi/php-lib/badge.svg?branch=master)](https://coveralls.io/github/pdfkiwi/php-lib?branch=master)

## Installation

```bash
$ composer require "pdfkiwi/php-lib"
```

Then, don't forget to use the `PdfKiwi` namespace in your own classes before using it:

```php
<?php
use PdfKiwi\PdfKiwi;
```

## Quick start guide

When instanciating the `Pdfkiwi` class, we must give it the __email address__ and the __API key (token)__
to be able to connect the API with our account's credentials.

Here is an exemple:

```php
<?php
use PdfKiwi\PdfKiwi;

$pdfKiwi = new PdfKiwi($email, $token);
```
Then we can set some options, this way:

```php
<?php
$pdfKiwi->setPageMargins('10mm', '20mm', '10mm', '20mm');
$pdfKiwi->setOrientation('landscape');
```

Finally, once we have defined all our options, we can fire the method `convertHtml()` which returns
the converted PDF:

```php
<?php
$pdf = $pdfKiwi->convertHtml($html);
```

## Public methods list

### setPageWidth()

To set the page width.

Parameter:

```
$value (string) The document's page width, with unit (eg. '210mm')
```

### setPageHeight()

To set the page height.

Parameter:

```
$value (string) The document's page height, with unit (eg. '297mm')
```

### setHeaderHtml()

To set page header using HTML format.

Parameter:

```
$value (string) The HTML content of the header
```

### setHeaderSpacing()

To set the space between the header and the document's body.

Parameter:

```
$value (float) The space (in mm) between the header and the body, without unit
```

### setHeaderText()

To set page headers using TEXT format.

Parameters:

```
$value (mixed) Can be either :
  - (string) The (text) content of the header (will be left aligned)
  - (array) An array with 3 values of text, respectively : [left, center, right]
```

### setFooterHtml()

To set page footer using HTML format.

Parameter:

```
$value (string) The HTML content of the footer
```

### setFooterSpacing()

To set the space between the footer and the document's body.

Parameter:

```
$value (float) The space (in mm) between the footer and the body, without unit
```

### setFooterText()

To set page footers using TEXT format.

Parameters:

```
$value (mixed) Can be either :
  - (string) The (text) content of the footer (will be left aligned)
  - (array) An array with 3 values of text, respectively : [left, center, right]
```

### setHeaderFooterPageExcludeList()

To set a list of pages numbers on which the header and footer won't be printed.

Parameter:

```
$value (mixed) Can be either :
  - (string) A comma separated list of page numbers (without space, eg. '1,3,5')
  - (array) An array with a list of page numbers (eg. [1, 3, 5])
```

### setPageMargins()

To set document's pages margins.

Parameters:

```
$top (string) Top margin value, with unit (eg. '20mm')
$right (string) Right margin value, with unit (eg. '20mm')
$bottom (string) Bottom margin value, with unit (eg. '20mm')
$left (string) Left margin value, with unit (eg. '20mm')
```

### setOrientation()

To set page's orientation.

Parameter:

```
$orientation (string) The page's orientation. Can be either 'landscape', or 'portrait'.
```
