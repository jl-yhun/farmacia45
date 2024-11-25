<?php

namespace App\Helpers;

use stdClass;

class Sanitizer implements SanitizerBuilder
{
    private $_instance;

    private function instanceIfMissing(): void
    {
        if (!isset($_instance))
            $this->_instance = new stdClass();
        $this->_instance = $this;
    }

    public function rmAcentos(): SanitizerBuilder
    {
        $this->instanceIfMissing();
        $this->_instance->acentos = false;
        return $this;
    }

    public function trim(): SanitizerBuilder
    {

        $this->instanceIfMissing();
        $this->_instance->trim = true;
        return $this;
    }

    public function doUpperCase(): SanitizerBuilder
    {

        $this->instanceIfMissing();
        $this->_instance->case = 'upper';
        return $this;
    }

    public function doLowerCase(): SanitizerBuilder
    {

        $this->instanceIfMissing();
        $this->_instance->case = 'lower';
        return $this;
    }

    private function fillDefaultValues()
    {
        $this->_instance->acentos = $this->_instance->acentos ?? true;
        $this->_instance->enies = $this->_instance->enies ?? true;
        $this->_instance->trim = $this->_instance->trim ?? false;
        $this->_instance->case = $this->_instance->case ?? '';
    }

    public function apply(string $value): string
    {
        $this->fillDefaultValues();

        if (!$this->_instance->acentos)
            $value = $this->removeAccents($value);

        if ($this->_instance->case == 'upper')
            $value = mb_strtoupper($value);
        else if ($this->_instance->case == 'lower')
            $value = mb_strtolower($value);

        if ($this->_instance->trim)
            $value = trim($value);


        return $value;
    }

    private function removeAccents($value): string
    {
        $str = htmlentities($value, ENT_COMPAT, "UTF-8");
        $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $str);
        return html_entity_decode($str);
    }
}
