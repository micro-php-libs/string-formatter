<?php
/**
 * This file is part of DBCSoft Standard Package
 *
 * (c) Ty Huynh <hongty.huynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MicroPhpLibs\StringFormatter\Contracts;


interface FormatterContract
{
    /**
     * process the formatter rules.
     *
     * @return $this
     */
    public function format();

    /**
     * Parse the data array, converting dots and asterisks.
     *
     * @param  array  $data
     * @return array
     */
    public function parseData(array $data);

    /**
     * @param $attribute
     * @param $rules
     * @return mixed
     */
    public function hasRule($attribute, $rules);

    /**
     * Get the formatter rules.
     *
     * @return array
     */
    public function getRules();

    /**
     * Set the formatter rules.
     *
     * @param  array  $rules
     * @return $this
     */
    public function setRules(array $rules);

    /**
     * Parse the given rules and merge them into current rules.
     *
     * @param  array  $rules
     * @return void
     */
    public function addRules($rules);

    /**
     * Get the attributes and values that were formatted.
     *
     * @return array
     *
     * @throws \App\Components\Formatter\FormatterException
     */
    public function formatted();

    /**
     * Get the data under formatter.
     *
     * @return array
     */
    public function attributes();

    /**
     * Get the data under formatter.
     *
     * @return array
     */
    public function getData();

    /**
     * Get the data under formatted.
     *
     * @return array
     */
    public function getFormattedData();

    /**
     * Set the data under formatter.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData(array $data);
}
