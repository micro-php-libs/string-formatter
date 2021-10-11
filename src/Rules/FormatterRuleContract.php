<?php
/**
 * This file is part of DBCSoft Standard Package
 *
 * (c) Ty Huynh <hongty.huynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MicroPhpLibs\RavelFormatter\Rules;


use MicroPhpLibs\RavelFormatter\Formatter;

interface FormatterRuleContract
{

    /**
     * @param Formatter $formatter
     * @return mixed
     */
    public function setFormatter(Formatter $formatter);

    /**
     * @param string $attribute
     * @param mixed $value
     * @return mixed
     */
    public function format($attribute, $value);
}
