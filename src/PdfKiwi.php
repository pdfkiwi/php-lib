<?php
namespace PdfKiwi;

class PdfKiwi
{
    public static $libVersion = "0.1.7";
    public static $apiHost    = 'https://pdf.kiwi';

    private $apiPrefix;
    private $userAgent;
    private $fields;
    private $httpResponseCode;
    private $errorNumber;
    private $errorMessage;

    /**
     * PdfKiwi constructor
     *
     * @param string $email Email address of a pdf.kiwi customer
     * @param string $apiToken Api key of the choosen customer's subscription
     */
    public function __construct($email, $apiToken)
    {
        $this->apiPrefix = sprintf('%s/api', self::$apiHost);

        $this->fields = [
            'email'   => $email,
            'token'   => $apiToken,
            'options' => []
        ];

        $this->userAgent = sprintf('pdfkiwi_php_client_%s', self::$libVersion);
    }

    /**
     * To set the page width
     *
     * @param string $value The document's page width, with unit (eg. '210mm')
     */
    public function setPageWidth($value)
    {
        $this->fields['options']['width'] = $value;
    }

    /**
     * To set the page height
     *
     * @param string $value The document's page height, with unit (eg. '297mm')
     */
    public function setPageHeight($value)
    {
        $this->fields['options']['height'] = $value;
    }

    /**
     * To set a HTML page header
     *
     * @param string $value The HTML content of the header
     */
    public function setHeaderHtml($value)
    {
        $this->fields['options']['header_html'] = $value;
    }


    /**
     * To set the space between the header and the document's body
     *
     * @param float $value the space (in mm) between the header and the body, without unit
     */
    public function setHeaderSpacing($value)
    {
        if ((float)$value) {
            $this->fields['options']['header_spacing'] = (float)$value;
        }
    }

    /**
     * To set some TEXT page headers
     *
     * @param mixed $value Can be either :
     *      - string The (text) content of the header (will be left aligned)
     *      - array  An array with 3 values of text, respectively : [left, center, right]
     */
    public function setHeaderText($value)
    {
        if (is_array($value)) {
            $this->fields['options']['header_text_left']   = $value[0];
            $this->fields['options']['header_text_center'] = $value[1];
            $this->fields['options']['header_text_right']  = $value[2];
        } else {
            $this->fields['options']['header_text_left'] = $value;
        }
    }

    /**
     * To set a HTML page footer
     *
     * @param string $value The HTML content of the footer
     */
    public function setFooterHtml($value)
    {
        $this->fields['options']['footer_html'] = $value;
    }

    /**
     * To set the space between the footer and the document's body
     *
     * @param float $value the space (in mm) between the footer and the body, without unit
     */
    public function setFooterSpacing($value)
    {
        if ((float)$value) {
            $this->fields['options']['footer_spacing'] = (float)$value;
        }
    }

    /**
     * To set some TEXT page footers
     *
     * @param mixed $value Can be either :
     *      - string The (text) content of the footer (will be left aligned)
     *      - array  An array with 3 values of text, respectively : [left, center, right]
     */
    public function setFooterText($value)
    {
        if (is_array($value)) {
            $this->fields['options']['footer_text_left']   = $value[0];
            $this->fields['options']['footer_text_center'] = $value[1];
            $this->fields['options']['footer_text_right']  = $value[2];
        } else {
            $this->fields['options']['footer_text_left'] = $value;
        }
    }

    /**
     * To set a list of pages numbers on which the header and footer won't be printed
     *
     * @param mixed $value Can be either :
     *      - string A comma separated list of page numbers (without space, eg. '1,3,5')
     *      - array  An array with a list of page numbers (eg. [1, 3, 5])
     */
    public function setHeaderFooterPageExcludeList($value)
    {
        if (is_array($value)) {
            $this->fields['options']['header_footer_exclude_pages'] = implode(',', $value);
        } else {
            $this->fields['options']['header_footer_exclude_pages'] = $value;
        }
    }

    /**
     * To set page's margins
     *
     * @param string $top Top margin value, with unit (eg. '20mm')
     * @param string $right Right margin value, with unit (eg. '20mm')
     * @param string $bottom Bottom margin value, with unit (eg. '20mm')
     * @param string $left Left margin value, with unit (eg. '20mm')
     */
    public function setPageMargins($top, $right, $bottom, $left)
    {
        $this->fields['options']['margin_top']    = $top;
        $this->fields['options']['margin_right']  = $right;
        $this->fields['options']['margin_bottom'] = $bottom;
        $this->fields['options']['margin_left']   = $left;
    }

    /**
     * To set page's orientation
     *
     * @param string $orientation The page's orientation. Can be either 'landscape', or 'portrait'
     */
    public function setOrientation($orientation)
    {
        $this->fields['options']['orientation'] = $orientation;
    }

    /**
     * To convert an HTML string into PDF (calling pdf.kiwi API)
     *
     * @param string $src The HTML document's content to convert
     * @param string $outstream If null, returns a string containing the PDF
     *                          If defined, save the output into a file. It must set a path and filename
     * @return string The result of the PDF conversion
     */
    public function convertHtml($src, $outstream = null)
    {
        if (empty($src)) {
            throw new PdfKiwiException("convertHTML(): the src parameter must not be empty!");
        }

        $this->fields['html'] = $src;

        $uri        = sprintf('%s/convert/html/', $this->apiPrefix);
        $postfields = http_build_query($this->fields);

        return $this->__httpPost($uri, $postfields, $outstream);
    }

    // ——————————————————————————————————————————————————————
    // —
    // —    Private Methods
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
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        if ($outstream) {
            $this->outstream = $outstream;
            curl_setopt($curl, CURLOPT_WRITEFUNCTION, [$this, '__receiveToStream']);
        }

        $this->httpResponseCode = 0;

        $response = curl_exec($curl);

        $this->httpResponseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->errorNumber      = curl_errno($curl);
        $this->errorMessage     = curl_error($curl);

        curl_close($curl);

        if ($this->errorNumber !== 0) {
            throw new PdfKiwiException($this->errorMessage, $this->errorNumber);
        }

        if ($this->httpResponseCode !== 200) {
            $jsonResponse = json_decode($response, true);
            throw new PdfKiwiException(
                ($jsonResponse) ? $jsonResponse['error']['message'] : $response,
                ($jsonResponse) ? $jsonResponse['error']['code'] : $this->httpResponseCode
            );
        }

        return $response;
    }

    private function __receiveToStream($curl, $data)
    {
        if ($this->httpResponseCode === 0) {
            $this->httpResponseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }

        if ($this->httpResponseCode >= 400) {
            $this->errorMessage = $this->errorMessage . $data;
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
                throw new PdfKiwiException("Writing the PDF to output stream failed.");
            }
        }

        return $written;
    }
}
