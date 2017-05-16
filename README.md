Utilitaire PHP pour utiliser l'API de pdf.kiwi
---

### Installation
Pour utiliser la librairie PHP de pdf.kiwi, il faut ajouter ces lignes au `composer.json` de vos projets :

    "repositories": [
        {
            "type" : "git",
            "url"  : "git@gitlab.insolus.net:pdf.kiwi/php-lib.git"
        }
    ],

Et, dans la section `require` :

    "pdfkiwi/php-lib" : "~{version choisie}"

### Guide de démarrage rapide
Lors de l'instanciation de l'objet PdfKiwi, il faut lui donner l'**adresse email** et le **token** pour l'autorisation d'accès à l'API.  
Il est aussi possible de lui passer l'adresse de l'accès à l'api. Si ce paramètre est omis, PdfKiwi utilisera l'adresse 'pdf.kiwi'.

Voici un exemple :

    use PdfKiwi\PdfKiwi as PdfKiwi;
    
    (...)

    $conf = Configure::read('PdfKiwi');

    $pdfKiwi = new PdfKiwi($conf['api']['email'], $conf['api']['token'], $conf['api']['host']);

Ensuite, on peut lui attribuer des options, de la manière suivante :

    $pdfKiwi->setOrientation('landscape');

Enfin, une fois toutes nos options définies, on peut appeler la méthode suivante qui retourne le PDF :

    $pdfKiwi->convertHtml($html);

### Liste des méthodes publiques
#### useSSL()
Pour utiliser SSL ou pas. Paramètres :

    $useSSL (boolean) true pour utiliser SSL, false sinon.
    
Cette méthode est appelée lors de l'instanciation, avec `false` par défaut.
Pour utiliser SSL, il faut donc l'appeler après avoir créé l'instance, en lui passant `true`.

#### setPageWidth()
Pour définir la largeur de page. Paramètre :

    $value (string) La largeur du document, avec l'unité (ex. '210mm')

#### setPageHeight()
Pour définir la hauteur de page. Paramètre :

    $value (string) La hauteur du document, avec l'unité (ex. '297mm')

#### setHeaderHtml()
Pour définir un en-tête de page au format HTML. Paramètre :

    $value (string) Le contenu HTML du header

#### setFooterHtml()
Pour définir un pied de page au format HTML. Paramètre :

    $value (string) Le contenu HTML du footer

#### setHeaderSpacing()
Pour définir l'espacement entre l'en-tête de page et le corps du document. Paramètre :

    $value (float) L'espacement entre le header et le body, en mm. (ne pas donner l'unité)

#### setFooterSpacing()
Pour définir l'espacement entre le pied de page et le corps du document. Paramètre :

    $value (float) L'espacement entre le footer et le body, en mm. (ne pas donner l'unité)

#### setPageMargins()
Pour définir les marges des pages. Paramètres :

    $top    (string) La valeur de la marge du haut, avec l'unité (ex. '20mm')
    $right  (string) La valeur de la marge de droite, avec l'unité (ex. '20mm')
    $bottom (string) La valeur de la marge du bas, avec l'unité (ex. '20mm')
    $left   (string) La valeur de la marge de gauche, avec l'unité (ex. '20mm')

#### setOrientation()
Pour définir l'orientation des pages. Paramètre :

    $orientation (string) L'orientation des pages. Doit être soit 'landscape', soit 'portrait'.
                          La valeur 'paysage' est autorisée et correspond à 'landscape'.

---

### Guide de transition depuis PdfCrowd vers pdf.kiwi
D'une manière générale, nous avons essayé de garder un maximum de méthodes similaires à ce que propose la librairie [pdfcrowd.php](https://github.com/pdfcrowd/pdfcrowd-php/), afin de faciliter la transition vers **Pdf.Kiwi**. Cependant, certaines options qui n'étaient pas disponibles avec PdfCrowd, le sont avec Pdf.Kiwi. Aussi, la façon de l'importer au projet est différente.

Voici la liste des choses à faire pour passer à Pdf.Kiwi :

#### Importation de la librairie
Si vous installez la librairie `pdfkiwi/php-lib` via Composer, il n'est plus nécessaire d'appeler la méthode Cake `App::import()` pour inclure la classe. Il suffit d'ajouter la ligne suivante avant la déclaration de votre classe qui génére les PDF :

    use PdfKiwi\PdfKiwi;

#### Orientation
Il n'est plus nécessaire de redéfinir (inverser) la largeur et la hauteur de page quand on veut une orientation au format "paysage". Il suffit maintenant d'appeler la méthode `setOrientation()` avec l'orientation choisie (voir *liste des méthodes publiques*, ci-dessus).
