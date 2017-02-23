<?php
namespace PdfKiwi;

class PdfKiwi
{
    public static $client_version = "0.1.0";
    public static $http_port      = 80;
    public static $https_port     = 443;
    public static $api_host       = 'pdf.kiwi';

    private $hostname;
    private $fields;
    private $scheme;
    private $api_prefix;
    private $port;
    private $http_code;
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
            $this->hostname = self::$api_host;
        }
        $this->useSSL(false);

        $this->fields = [
            'email'   => $email,
            'token'   => $apiToken,
            'options' => []
        ];

        $this->user_agent = "pdfkiwi_php_client_".self::$client_version;
    }

    /**
     * Pour utiliser SSL ou pas
     *
     * @param boolean $useSSL True pour utiliser SSL, false sinon.
     */
    public function useSSL($useSSL)
    {
        if ($useSSL) {
            $this->port = self::$https_port;
            $this->scheme = 'https';
        } else {
            $this->port = self::$http_port;
            $this->scheme = 'http';
        }

        $this->api_prefix = "{$this->scheme}://{$this->hostname}/api";
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
        $uri = sprintf('%s/generator/render/', $this->api_prefix);
        $postfields = http_build_query($this->fields);
        return $this->httpPost($uri, $postfields, $outstream);
    }

    // ——————————————————————————————————————————————————————
    // —
    // —    Méthodes privées
    // —
    // ——————————————————————————————————————————————————————

    private function httpPost($url, $postfields, $outstream)
    {
        if (!function_exists("curl_init")) {
            throw new PdfKiwiException("PdfKiwi requires php-curl extension, which is not installed on your system.");
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_PORT, $this->port);
        curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($c, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($c, CURLOPT_USERAGENT, $this->user_agent);

        if ($outstream) {
            $this->outstream = $outstream;
            curl_setopt($c, CURLOPT_WRITEFUNCTION, [$this, 'receiveToStream']);
        }

        if ($this->scheme === 'https' && self::$api_host === 'pdf.kiwi') {
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        }

        $this->http_code = 0;
        $this->error     = "";

        $response        = curl_exec($c);
        $this->http_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $error_message   = curl_error($c);
        $error_number    = curl_errno($c);

        curl_close($c);

        if ($error_number !== 0) {
            throw new PdfKiwiException($error_message, $error_number);
        }

        if ($this->http_code !== 200) {
            $jsonResponse = json_decode($response, true);
            if (isset($jsonResponse['error']['message'])) {
                throw new PdfKiwiException($jsonResponse['error']['message'], $this->http_code);
            } else {
                throw new PdfKiwiException($this->error ? $this->error : $response, $this->http_code);
            }
        }

        if (!$outstream) {
            return $response;
        }
    }

    private function receiveToStream($curl, $data)
    {
        if ($this->http_code === 0) {
            $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }

        if ($this->http_code >= 400) {
            $this->error = $this->error . $data;
            return strlen($data);
        }

        $written = fwrite($this->outstream, $data);

        if ($written != strlen($data)) {
            if (get_magic_quotes_runtime()) {
                throw new PdfKiwiException("Cannot write the PDF file because the 'magic_quotes_runtime' setting is enabled.
Please disable it either in your php.ini file, or in your code by calling 'set_magic_quotes_runtime(false)'.");
            } else {
                throw new PdfKiwiException('Writing the PDF file failed.');
            }
        }
        return $written;
    }
}
