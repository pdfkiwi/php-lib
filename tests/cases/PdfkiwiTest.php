<?php
use PHPUnit\Framework\TestCase;
use PdfKiwi\PdfKiwi;
use PdfKiwi\PdfKiwiException;
use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use Symfony\Component\HttpFoundation\Response;

class PdfkiwiTest extends TestCase
{
    use HttpMockTrait;

    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass('28080', 'localhost');
    }

    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
    }

    public function setUp()
    {
        $this->pdfKiwi    = new PdfKiwi('test@test.org', 'ca4e46cf6f31155e25dd1168ecbc38f9');
        $pdfKiwiReflected = new ReflectionClass('PdfKiwi\PdfKiwi');

        $apiHostProperty = $pdfKiwiReflected->getProperty('apiHost');
        $apiHostProperty->setAccessible(true);
        $apiHostProperty->setValue('localhost');

        $apiHostProperty = $pdfKiwiReflected->getProperty('apiPort');
        $apiHostProperty->setAccessible(true);
        $apiHostProperty->setValue('28080');

        $this->fields = $pdfKiwiReflected->getProperty('fields');
        $this->fields->setAccessible(true);

        $this->tempFile = '/tmp/test.pdf';

        $this->setUpHttpMock();
    }

    public function tearDown()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }

        $this->tearDownHttpMock();
    }

    public function testSetPageWidth()
    {
        // - Page width
        $this->pdfKiwi->setPageWidth('150mm');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'width' => '150mm'
            ]
        ]);
    }

    public function testSetPageHeight()
    {
        // - Page height
        $this->pdfKiwi->setPageHeight('200mm');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'height' => '200mm'
            ]
        ]);
    }

    public function testSetHeaderHtml()
    {
        // - HTML formated header
        $this->pdfKiwi->setHeaderHtml('<p>Test header</p>');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'header_html' => '<p>Test header</p>'
            ]
        ]);
    }

    public function testSetHeaderSpacing()
    {
        // - If it's not a number, the option is ignored
        $this->pdfKiwi->setHeaderSpacing('NAN');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => []
        ]);

        // - If it's a number (float or not), the option is set
        $this->pdfKiwi->setHeaderSpacing(2.5);
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'header_spacing' => 2.5
            ]
        ]);
    }

    public function testSetHeaderText()
    {
        // - Text header with simple text value -> left aligned header
        $this->pdfKiwi->setHeaderText('Simple text header');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'header_text_left' => 'Simple text header'
            ]
        ]);

        // - Text header with 3-part array of text values
        $this->pdfKiwi->setHeaderText([
            'Left aligned header',
            'Center of header',
            'Right part of header'
        ]);
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'header_text_left'   => 'Left aligned header',
                'header_text_center' => 'Center of header',
                'header_text_right'  => 'Right part of header'
            ]
        ]);
    }

    public function testSetFooterHtml()
    {
        // - HTML formated footer
        $this->pdfKiwi->setFooterHtml('<p>Test footer</p>');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'footer_html' => '<p>Test footer</p>'
            ]
        ]);
    }

    public function testSetFooterSpacing()
    {
        // - If it's not a number, the option is ignored
        $this->pdfKiwi->setFooterSpacing('NAN');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => []
        ]);

        // - If it's a number (float or not), the option is set
        $this->pdfKiwi->setFooterSpacing(2.5);
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'footer_spacing' => 2.5
            ]
        ]);
    }

    public function testSetFooterText()
    {
        // - Text footer with simple text value -> left aligned footer
        $this->pdfKiwi->setFooterText('Simple text footer');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'footer_text_left' => 'Simple text footer'
            ]
        ]);

        // - Text footer with 3-part array of text values
        $this->pdfKiwi->setFooterText([
            'Left aligned footer',
            'Center of footer',
            'Right part of footer'
        ]);
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'footer_text_left'   => 'Left aligned footer',
                'footer_text_center' => 'Center of footer',
                'footer_text_right'  => 'Right part of footer'
            ]
        ]);
    }

    public function testSetHeaderFooterPageExcludeList()
    {
        // - List of page where to exclude header and footer,
        // - using a string of comma separated numbers
        $this->pdfKiwi->setHeaderFooterPageExcludeList('1,3,5');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'header_footer_exclude_pages' => '1,3,5'
            ]
        ]);

        // - List of page where to exclude header and footer, using an array
        $this->pdfKiwi->setHeaderFooterPageExcludeList([1, 3, 5]);
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'header_footer_exclude_pages' => '1,3,5'
            ]
        ]);
    }

    public function testSetPageMargins()
    {
        // - Page margins, with all values (top, right, bottom, left)
        $this->pdfKiwi->setPageMargins('10mm', '5mm', '20mm', '8mm');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'margin_top'    => '10mm',
                'margin_right'  => '5mm',
                'margin_bottom' => '20mm',
                'margin_left'   => '8mm'
            ]
        ]);
    }

    public function testSetOrientation()
    {
        // - Orientation
        $this->pdfKiwi->setOrientation('landscape');
        $this->assertEquals($this->fields->getValue($this->pdfKiwi), [
            'email'   => 'test@test.org',
            'token'   => 'ca4e46cf6f31155e25dd1168ecbc38f9',
            'options' => [
                'orientation' => 'landscape'
            ]
        ]);
    }

    public function testConvertHtmlFailNotEmpty()
    {
        // - Send html to pdf.kiwi API
        $this->expectExceptionMessage('convertHTML(): the src parameter must not be empty!');
        $this->pdfKiwi->convertHtml('');
    }

    public function testConvertHtmlToPdfString()
    {
        $pdfResponse = 'This emulate a well formed PDF, which contains the title "Testing pdf.kiwi".';

        $this->http->mock->when()
            ->methodIs('POST')
            ->pathIs('/api/convert/html/')
            ->then()
            ->statusCode(Response::HTTP_OK)
            ->body($pdfResponse)
            ->end();
        $this->http->setUp();

        // - Converting HTML to PDF and get the response as string
        $result = $this->pdfKiwi->convertHtml('<h1>Testing pdf.kiwi</h1>');
        $this->assertEquals($result, $pdfResponse);
    }

    // public function testConvertHtmlToPdfFile()
    // {
    //     $pdfResponse = 'This emulate a well formed PDF, which contains the title "Testing pdf.kiwi".';
    //
    //     $this->http->mock->when()
    //         ->methodIs('POST')
    //         ->pathIs('/api/convert/html/')
    //         ->then()
    //         ->statusCode(Response::HTTP_OK)
    //         ->body($pdfResponse)
    //         ->end();
    //     $this->http->setUp();
    //
    //     // - Converting HTML to PDF and save the response into a file
    //     $fileResource = fopen($this->tempFile, 'w');
    //     $result       = $this->pdfKiwi->convertHtml('<h1>Testing pdf.kiwi</h1>', $fileResource);
    //     $this->assertEquals($result, true);
    //     $this->assertEquals(file_get_contents($this->tempFile), $pdfResponse);
    // }
    //
    // public function testConvertHtmlToPdfStringUnauthorized()
    // {
    //     $responseBody = [
    //         'success' => false,
    //         'error'   => [
    //             'message' => 'The email address does not correspond to any account.',
    //             'code'    => 40
    //         ]
    //     ];
    //     $this->http->mock->when()
    //         ->methodIs('POST')
    //         ->pathIs('/api/convert/html/')
    //         ->then()
    //         ->statusCode(Response::HTTP_UNAUTHORIZED)
    //         ->body(json_encode($responseBody))
    //         ->end();
    //     $this->http->setUp();
    //
    //     // - Fail because of bad credentials
    //     $this->expectExceptionMessage('The email address does not correspond to any account.');
    //     $this->pdfKiwi->convertHtml('<h1>Testing pdf.kiwi</h1>');
    // }
    //
    // public function testConvertHtmlToPdfFileUnauthorized()
    // {
    //     $responseBody = [
    //         'success' => false,
    //         'error'   => [
    //             'message' => 'The email address does not correspond to any account.',
    //             'code'    => 40
    //         ]
    //     ];
    //     $this->http->mock->when()
    //         ->methodIs('POST')
    //         ->pathIs('/api/convert/html/')
    //         ->then()
    //         ->statusCode(Response::HTTP_UNAUTHORIZED)
    //         ->body(json_encode($responseBody))
    //         ->end();
    //     $this->http->setUp();
    //
    //     // - Fail because of bad credentials
    //     $fileResource = fopen($this->tempFile, 'w');
    //     $this->expectExceptionMessage('The email address does not correspond to any account.');
    //     $this->pdfKiwi->convertHtml('<h1>Testing pdf.kiwi</h1>', $fileResource);
    // }
}
