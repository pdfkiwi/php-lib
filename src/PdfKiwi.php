<?php
namespace PdfKiwi;

class PdfKiwi
{
    public static $libVersion = "0.1.1";
    public static $httpPort   = 80;
    public static $httpsPort  = 443;
    public static $apiHost    = 'pdf.kiwi';

    private $hostname;
    private $userAgent;
    private $fields;
    private $scheme;
    private $apiPrefix;
    private $port;
    private $httpCode;
    private $error;

    /**
     * PdfKiwi constructor
     *
     * @param string $email L'adresse email du client
     * @param string $apiToken Le token du client
     * @param string $hostname L'adresse de l'API de pdf.kiwi (par défaut 'pdf.kiwi')
     */
    public function __construct($email, $apiToken, $hostname = null)
    {
        if ($hostname) {
            $this->hostname = $hostname;
        } else {
            $this->hostname = self::$apiHost;
        }
        $this->useSSL(false);

        $this->fields = [
            'email'   => $email,
            'token'   => $apiToken,
            'options' => []
        ];

        $this->userAgent = sprintf('pdfkiwi_php_client_%s', self::$libVersion);
    }

    /**
     * Pour utiliser SSL ou pas
     *
     * @param boolean $useSSL True pour utiliser SSL, false sinon.
     */
    public function useSSL($useSSL)
    {
        if ($useSSL) {
            $this->port   = self::$httpsPort;
            $this->scheme = 'https';
        } else {
            $this->port   = self::$httpPort;
            $this->scheme = 'http';
        }

        $this->apiPrefix = sprintf('%s://%s/api', $this->scheme, $this->hostname);
    }

    /**
     * Pour définir la largeur de page
     *
     * @param string $value La largeur du document, avec l'unité (ex. 210mm)
     */
    public function setPageWidth($value)
    {
        $this->fields['options']['width'] = $value;
    }

    /**
     * Pour définir la hauteur de page
     *
     * @param string $value La hauteur du document, avec l'unité (ex. 297mm)
     */
    public function setPageHeight($value)
    {
        $this->fields['options']['height'] = $value;
    }

    /**
     * Pour définir un en-tête de page au format HTML
     *
     * @param string $value Le contenu HTML du header
     */
    public function setHeaderHtml($value)
    {
        $this->fields['options']['header_html'] = $value;
    }

    /**
     * Pour définir un pied de page au format HTML
     *
     * @param string $value Le contenu HTML du footer
     */
    public function setFooterHtml($value)
    {
        $this->fields['options']['footer_html'] = $value;
    }

    /**
     * Pour définir les marges des pages
     *
     * @param string $top La valeur de la marge du haut, avec l'unité (ex. 20mm)
     * @param string $right La valeur de la marge de droite, avec l'unité (ex. 20mm)
     * @param string $bottom La valeur de la marge du bas, avec l'unité (ex. 20mm)
     * @param string $left La valeur de la marge de gauche, avec l'unité (ex. 20mm)
     */
    public function setPageMargins($top, $right, $bottom, $left)
    {
        $this->fields['options']['margin_top']    = $top;
        $this->fields['options']['margin_right']  = $right;
        $this->fields['options']['margin_bottom'] = $bottom;
        $this->fields['options']['margin_left']   = $left;
    }

    /**
     * Pour définir l'orientation des pages
     *
     * @param string $orientation L'orientation de la page. Soit 'landscape', soit 'portrait'
     */
    public function setOrientation($orientation)
    {
        $this->fields['options']['orientation'] = $orientation;
    }

    /**
     * Pour convertir un document HTML se trouvant en mémoire
     *
     * @param string $src Le contenu du document HTML
     * @param string $outstream Si défini, enregistre la sortie dans un fichier
     *                          Si null, retourne une chaîne contenant le PDF
     * @return string Le résultat de la conversion en PDF
     */
    public function convertHtml($src, $outstream = null)
    {
        if (empty($src)) {
            throw new PdfKiwiException("convertHTML(): the src parameter must not be empty!");
        }

        $this->fields['html'] = $src;
        $uri = sprintf('%s/generator/render/', $this->apiPrefix);
        $postfields = http_build_query($this->fields);
        return $this->__httpPost($uri, $postfields, $outstream);
    }

    // ——————————————————————————————————————————————————————
    // —
    // —    Méthodes privées
    // —
    // ——————————————————————————————————————————————————————

    private function __httpPost($url, $postfields, $outstream)
    {
        if (!function_exists("curl_init")) {
            throw new PdfKiwiException("PdfKiwi requires php-curl extension, which is not installed on your system.");
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_PORT, $this->port);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);

        if ($outstream) {
            $this->outstream = $outstream;
            curl_setopt($curl, CURLOPT_WRITEFUNCTION, [$this, '__receiveToStream']);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, ($this->scheme === 'https' && self::$apiHost === 'pdf.kiwi'));

        $this->httpCode = 0;
        $this->error    = "";

        $response       = curl_exec($curl);
        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $errorMessage   = curl_error($curl);
        $errorNumber    = curl_errno($curl);

        curl_close($curl);

        if ($errorNumber !== 0) {
            throw new PdfKiwiException($errorMessage, $errorNumber);
        }

        if ($this->httpCode !== 200) {
            $jsonResponse = json_decode($response, true);
            if (isset($jsonResponse['error']['message'])) {
                throw new PdfKiwiException($jsonResponse['error']['message'], $this->httpCode);
            } else {
                throw new PdfKiwiException($this->error ? $this->error : $response, $this->httpCode);
            }
        }

        if (!$outstream) {
            return $response;
        }
    }

    private function __receiveToStream($curl, $data)
    {
        if ($this->httpCode === 0) {
            $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }

        if ($this->httpCode >= 400) {
            $this->error = $this->error . $data;
            return strlen($data);
        }

        $written = fwrite($this->outstream, $data);

        if ($written != strlen($data)) {
            if (get_magic_quotes_runtime()) {
                throw new PdfKiwiException(
                    "Cannot write the PDF file because the 'magic_quotes_runtime' setting is enabled." .
                    "Please disable it either in your php.ini file, or in your code by calling 'set_magic_quotes_runtime(false)'."
                );
            } else {
                throw new PdfKiwiException("Writing the PDF file failed.");
            }
        }
        return $written;
    }
}
