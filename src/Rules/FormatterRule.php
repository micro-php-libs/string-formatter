<?php


namespace MicroPhpLibs\StringFormatter\Rules;


use MicroPhpLibs\StringFormatter\Formatter;

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
