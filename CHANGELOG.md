## 0.2.0 (unreleased)
- URL de l'API hardcodé (suppression des paramètres 2 et 3 du constructor) (#11).
- Ajout des dépendances à PHPCS et Phpunit.
- Traduction de la documentation inline en Anglais (#15).

## 0.1.7 (2017-05-16)
- Suppression d'un fichier en doublon (`PdfKiwiException`) (#6)
- Mise à jour du Readme avec la nouvelle adresse du repository `pdf.kiwi` (#8)
- Normalisation du Changelog : ajout des dates de release (#7)

## 0.1.6 (2017-04-07)
- Support de l'option d'exclusion du header et du footer dans certaines pages : 
  `header_footer_exclude_pages` (#5) -> voir version 0.7.1 de l'API pdf.kiwi.

## 0.1.5 (2017-04-06)
- Support des options d'espacement du header et du footer : `header_spacing` et 
  `footer_spacing` (#4) -> voir version 0.7.0 de l'API pdf.kiwi.

## 0.1.4 (2017-03-28)
- Utilisation de SSL pour l'adresse par défaut de l'API (https://pdf.kiwi) (#3)

## 0.1.3 (2017-03-03)
- Ajout des méthodes `setHeaderText()` et `setFooterText()` (#2)

## 0.1.2 (2017-02-24)
- Amélioration de la gestion des retours d'erreur API

## 0.1.1 (2017-02-23)
- Fix bug d'autoload : déplacement des fichiers dans src/ (après vérifications)

## 0.1.0 (2017-02-23)
- Création de la librairie PHP pour utiliser l'API de PdfKiwi avec les options de base
