pdfkiwi/php-lib
---

> Utilitaire PHP pour utiliser l'API de [pdf.kiwi](https://pdf.kiwi)

[![build status](https://gitlab.insolus.net/pdf.kiwi/php-lib/badges/master/build.svg)](https://gitlab.insolus.net/pdf.kiwi/php-lib/commits/master)
[![coverage report](https://gitlab.insolus.net/pdf.kiwi/php-lib/badges/master/coverage.svg)](https://gitlab.insolus.net/pdf.kiwi/php-lib/commits/master)

## Installation

```bash
$ composer require "pdfkiwi/php-lib"
```

## Guide de démarrage rapide

Lors de l'instanciation de l'objet PdfKiwi, il faut lui donner l'__adresse email__ et le __token__
pour l'autorisation d'accès à l'API.

Voici un exemple :

```php
<?php
use PdfKiwi\PdfKiwi as PdfKiwi;

$pdfKiwi = new PdfKiwi($email, $token);
```

Ensuite, on peut lui attribuer des options, de la manière suivante :

```php
<?php
$pdfKiwi->setOrientation('landscape');
```

Enfin, une fois toutes nos options définies, on peut appeler la méthode suivante qui retourne le PDF :

```php
<?php
$pdfKiwi->convertHtml($html);
```

## Liste des méthodes publiques

### useSSL()

Pour utiliser SSL ou pas.

Paramètres :

```
$useSSL (boolean) true pour utiliser SSL, false sinon.
```

Cette méthode est appelée lors de l'instanciation, avec `false` par défaut.
Pour utiliser SSL, il faut donc l'appeler après avoir créé l'instance, en lui passant `true`.

### setPageWidth()

Pour définir la largeur de page.

Paramètre :

```
$value (string) La largeur du document, avec l'unité (ex. '210mm')
```

### setPageHeight()

Pour définir la hauteur de page.

Paramètre :

```
$value (string) La hauteur du document, avec l'unité (ex. '297mm')
```

### setHeaderHtml()

Pour définir un en-tête de page au format HTML.

Paramètre :

```
$value (string) Le contenu HTML du header
```

### setFooterHtml()

Pour définir un pied de page au format HTML.

Paramètre :

```
$value (string) Le contenu HTML du footer
```

### setHeaderSpacing()

Pour définir l'espacement entre l'en-tête de page et le corps du document.

Paramètre :

```
$value (float) L'espacement entre le header et le body, en mm. (ne pas donner l'unité)
```

### setFooterSpacing()

Pour définir l'espacement entre le pied de page et le corps du document.

Paramètre :

```
$value (float) L'espacement entre le footer et le body, en mm. (ne pas donner l'unité)
```

### setPageMargins()

Pour définir les marges des pages.

Paramètres :

```
$top    (string) La valeur de la marge du haut, avec l'unité (ex. '20mm')
$right  (string) La valeur de la marge de droite, avec l'unité (ex. '20mm')
$bottom (string) La valeur de la marge du bas, avec l'unité (ex. '20mm')
$left   (string) La valeur de la marge de gauche, avec l'unité (ex. '20mm')
```

#### setOrientation()

Pour définir l'orientation des pages.

Paramètre :

```
$orientation (string) L'orientation des pages. Doit être soit 'landscape', soit 'portrait'.
                      La valeur 'paysage' est autorisée et correspond à 'landscape'.
```

---

## Guide de transition depuis PdfCrowd vers pdf.kiwi

D'une manière générale, nous avons essayé de garder un maximum de méthodes similaires à ce que propose
la librairie `pdfcrowd-php`, afin de faciliter la transition vers __pdf.kiwi__. 

Cependant, certaines options qui n'étaient pas disponibles avec PdfCrowd, le sont avec pdf.kiwi.  
Aussi, la façon de l'importer au projet est différente.

Voici la liste des choses à faire pour passer à pdf.kiwi :

### Importation de la librairie

Si vous installez la librairie `pdfkiwi/php-lib` via Composer,
il n'est plus nécessaire d'appeler la méthode Cake `App::import()` pour inclure la classe.  
Il suffit d'ajouter la ligne suivante avant la déclaration de votre classe qui génére les PDF :

```php
<?php
use PdfKiwi\PdfKiwi;
```

### Orientation

Il n'est plus nécessaire de redéfinir (inverser) la largeur et la hauteur 
de page quand on veut une orientation au format "paysage".  
Il suffit maintenant d'appeler la méthode `setOrientation()` avec l'orientation 
choisie (voir *liste des méthodes publiques*, ci-dessus).
