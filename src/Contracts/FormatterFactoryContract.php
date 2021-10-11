<?php
/**
 * This file is part of DBCSoft Standard Package
 *
 * (c) Ty Huynh <hongty.huynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MicroPhpLibs\RavelFormatter\Contracts;

use MicroPhpLibs\RavelFormatter\Formatter;

interface FormatterFactoryContract
{
    /**
     * Create a new Formatter instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $customAttributes
     * @return Formatter
     */
    public function make(array $data, array $rules, array $customAttributes = []);
}
