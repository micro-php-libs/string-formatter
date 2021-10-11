<?php


namespace MicroPhpLibs\RavelFormatter\Rules;


use MicroPhpLibs\RavelFormatter\Formatter;

abstract class FormatterRule implements FormatterRuleContract
{
    /** @var Formatter */
    protected $formatter;

    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public abstract function format($attribute, $value);
}
