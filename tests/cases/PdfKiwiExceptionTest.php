<?php
use PHPUnit\Framework\TestCase;
use PdfKiwi\PdfKiwiException;

class PdfKiwiExceptionTest extends TestCase
{
    public function testPdfKiwiExceptionToString()
    {
        $pdfKiwiException = new PdfKiwiException("message", 10);
        $result           = $pdfKiwiException->__toString();
        $this->assertEquals($result, "[10] message\n");

        $pdfKiwiException = new PdfKiwiException("message", 0);
        $result           = $pdfKiwiException->__toString();
        $this->assertEquals($result, "message\n");

        $pdfKiwiException = new PdfKiwiException("message");
        $result           = $pdfKiwiException->__toString();
        $this->assertEquals($result, "message\n");

        $pdfKiwiException = new PdfKiwiException();
        $result           = $pdfKiwiException->__toString();
        $this->assertEquals($result, "\n");
    }
}
