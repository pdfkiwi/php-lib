<?php
namespace PdfKiwi;

class PdfKiwiException extends \Exception
{
    public function __toString()
    {
        if ($this->code) {
            return sprintf("[%s] %s\n", $this->code, $this->message);
        } else {
            return sprintf("%s\n", $this->message);
        }
    }
}
