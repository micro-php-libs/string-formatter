<?php
/**
 * This file is part of DBCSoft Standard Package
 *
 * (c) Ty Huynh <hongty.huynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MicroPhpLibs\StringFormatter;



use MicroPhpLibs\StringFormatter\Contracts\FormatterFactoryContract;

class FormatterFactory implements FormatterFactoryContract
{
    protected $resolver;

    public function __construct()
    {

    }

    /**
     * Create a new Formatter instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $customAttributes
     * @return FormatterFactoryContract
     */
    public function make(array $data, array $rules, array $customAttributes = [])
    {
        $formatter = $this->resolve(
            $data, $rules, $customAttributes
        );

        return $formatter;
    }

    /**
     * Resolve a new Formatter instance.
     *
     * @param array $data
     * @param array $rules
     * @param array|null $customAttributes
     * @return Formatter|mixed
     */
    protected function resolve(array $data, array $rules, array $customAttributes)
    {
        // TODO: support resolver

        if (is_null($this->resolver)) {
            return new Formatter($data, $rules);
        }

        return call_user_func($this->resolver, $data, $rules, $customAttributes);
    }

}
