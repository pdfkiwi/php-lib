Contribuer au projet PdfKiwi/php-lib
---

### Philosophie
Pour le développement de cette librairie, il faut garder en tête le fait qu'elle remplace l'utilisation de *PdfCrowd*. Donc afin de faciliter le passage à *PdfKiwi* dans les projets, les méthodes de la classe `PdfKiwi` ont les même noms et responsabilités que celles de [pdfcrowd.php](https://github.com/pdfcrowd/pdfcrowd-php/blob/master/pdfcrowd.php).

### Changement de version
Lorsqu'on incrémente le numéro de version, ne pas oublier de mettre à jour le *composer.json*, mais **AUSSI** la variable `$libVersion` qui se trouve à la ligne 6 du fichier `src/PdfKiwi.php`.
