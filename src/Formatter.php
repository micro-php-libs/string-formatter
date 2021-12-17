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


use MicroPhpLibs\StringFormatter\Concerns\FormatAttributes;
use MicroPhpLibs\StringFormatter\Contracts\FormatterContract;
use MicroPhpLibs\StringFormatter\Rules\FormatterRuleContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Formatter implements FormatterContract
{
    use FormatAttributes;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * The data under formatter.
     *
     * @var array
     */
    protected $data;

    protected $formattedData;

    protected $dotPlaceholder;

    protected $currentRule;

    protected $excludeAttributes = [];
    protected $implicitAttributes = [];


    /**
     * Check string helper of Laravel
     * @var array
     * @ref https://laravel.com/docs/8.x/helpers
     */
    protected $implicitRules = [
        'Boolean',
        'Integer',
        'Float',
        'Accepted',
        'Substring',
        'Trim',
        'TrimEnd',
        'TrimStart',
        'Array',
        'Replace',
        'ReplaceFirst',
        'ReplaceLast',
        'Studly',
        'Title',
        'Upper',
        'Lower',
        'Limit',
        'Words',
        'Pad',
        'PadLeft',
        'PadRight'
    ];

    protected $excludeRules = [];
    protected $initialRules = [];

    public function __construct(array $data, array $rules)
    {
        $this->dotPlaceholder = Str::random();
        $this->data = $this->parseData($data);
        // NOTE: set & explode rules
        $this->setRules($rules);
    }

    /**
     * Parse the data array, converting dots and asterisks.
     *
     * @param  array  $data
     * @return array
     */
    public function parseData(array $data)
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->parseData($value);
            }

            $key = str_replace(
                ['.', '*'],
                [$this->dotPlaceholder, '__asterisk__'],
                $key
            );

            $newData[$key] = $value;
        }

        return $newData;
    }

    /**
     * process the formatter rules.
     */
    public function format()
    {
        // We'll spin through each rule, formatting the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);

                continue;
            }

            foreach ($rules as $rule) {
                $this->formatAttribute($attribute, $rule);

                if ($this->shouldBeExcluded($attribute)) {
                    $this->removeAttribute($attribute);

                    break;
                }

                if ($this->shouldStopFormatting($attribute)) {
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * @param string $attribute
     * @param string|FormatterRuleContract $rule
     */
    protected function formatAttribute($attribute, $rule)
    {
        $this->currentRule = $rule;

        [$rule, $parameters] = FormatterRuleParser::parse($rule);

        if ($rule == '') {
            return;
        }

        // If we have made it this far we will make sure the attribute is formattable and if it is
        // we will call the formatter method with the attribute. If a method returns false the
        // attribute is invalid
        $formattable = $this->isFormattable($rule, $attribute);
        $value = $this->getValue($attribute);
        // Allow custom formatter rule
        if ($rule instanceof FormatterRuleContract) {

            if ($formattable) {
                $value = $this->formatUsingCustomRule($attribute, $value, $rule);
            }

        } else {

            $method = "format{$rule}";

            if ($formattable) {
                $value = $this->$method($attribute, $value, $parameters, $this);
            }
        }

        $this->saveValue($attribute, $value);
    }

    /**
     * Determine if the attribute is formattable
     *
     * @param  object|string  $rule
     * @param  string  $attribute
     * @return bool
     */
    protected function isFormattable($rule, $attribute)
    {
        if (in_array($rule, $this->excludeRules)) {
            return true;
        }
        return $this->hasRule($attribute, $rule);
    }

    /**
     * Determine if a given rule implies the attribute is required.
     *
     * @param  object|string  $rule
     * @return bool
     */
    protected function isImplicit($rule)
    {
        return in_array($rule, $this->implicitRules);
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     *
     * @param  string  $attribute
     * @param  string|array  $rules
     * @return bool
     */
    public function hasRule($attribute, $rules)
    {
        return ! is_null($this->getRule($attribute, $rules));
    }

    /**
     * Get a rule and its parameters for a given attribute.
     *
     * @param  string  $attribute
     * @param  string|array  $rules
     * @return array|null
     */
    protected function getRule($attribute, $rules)
    {
        if (! array_key_exists($attribute, $this->rules)) {
            return;
        }

        if ($rules instanceof FormatterRuleContract) {

            return FormatterRuleParser::parse($rules);

        } else {

            $rules = (array) $rules;

            foreach ($this->rules[$attribute] as $rule) {

                [$rule, $parameters] = FormatterRuleParser::parse($rule);

                if (in_array($rule, $rules)) {
                    return [$rule, $parameters];
                }
            }

        }
    }

    /**
     * Get the formatter rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set the formatter rules.
     *
     * @param  array  $rules
     * @return $this
     */
    public function setRules(array $rules)
    {
//        $rules = collect($rules)->mapWithKeys(function ($value, $key) {
//            return [str_replace('\.', $this->dotPlaceholder, $key) => $value];
//        })->toArray();

        $this->initialRules = $rules;

        $this->rules = [];

        $this->addRules($rules);

        return $this;
    }

    /**
     * Parse the given rules and merge them into current rules.
     *
     * @param  array  $rules
     * @return void
     */
    public function addRules($rules)
    {
        // The primary purpose of this parser is to expand any "*" rules to the all
        // of the explicit rules needed for the given data. For example the rule
        // names.* would get expanded to names.0, names.1, etc. for this data.
        $response = (new FormatterRuleParser($this->data))
            ->explode($rules);

        $this->rules = array_merge_recursive(
            $this->rules, $response->rules
        );

        $this->implicitAttributes = array_merge(
            $this->implicitAttributes, $response->implicitAttributes
        );
    }

    /**
     * Get the attributes and values that were formatted.
     *
     * @return array
     *
     * @throws \MicroPhpLibs\StringFormatter\FormatterException
     */
    public function formatted()
    {
//        if ($this->invalid()) {
//            throw new FormatterException($this);
//        }

        $results = [];

        $missingValue = Str::random(10);

        foreach (array_keys($this->getRules()) as $key) {
            $value = data_get($this->getFormattedData(), $key, $missingValue);

            if ($value !== $missingValue) {
                Arr::set($results, $key, $value);
            }
        }

        return $this->replacePlaceholders($results);
    }

    /**
     * Determine if the attribute should be excluded.
     *
     * @param  string  $attribute
     * @return bool
     */
    protected function shouldBeExcluded($attribute)
    {
        foreach ($this->excludeAttributes as $excludeAttribute) {
            if ($attribute === $excludeAttribute ||
                Str::startsWith($attribute, $excludeAttribute.'.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove the given attribute.
     *
     * @param  string  $attribute
     *
     * @return void
     */
    protected function removeAttribute($attribute)
    {
        Arr::forget($this->data, $attribute);
        Arr::forget($this->formattedData, $attribute);
        Arr::forget($this->rules, $attribute);
    }

    /**
     * Get the data under formatter.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->getData();
    }

    /**
     * Get the data under formatter.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the data under formatted.
     *
     * @return array
     */
    public function getFormattedData()
    {
        return $this->formattedData;
    }

    /**
     * Set the data under formatter.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $this->parseData($data);

        $this->setRules($this->initialRules);

        return $this;
    }

    /**
     * Replace the placeholders used in data keys.
     *
     * @param  array  $data
     * @return array
     */
    protected function replacePlaceholders($data)
    {
        $originalData = [];

        foreach ($data as $key => $value) {
            $originalData[$this->replacePlaceholderInString($key)] = is_array($value)
                ? $this->replacePlaceholders($value)
                : $value;
        }

        return $originalData;
    }

    /**
     * Replace the placeholders in the given string.
     *
     * @param  string  $value
     * @return string
     */
    protected function replacePlaceholderInString(string $value)
    {
        return str_replace(
            [$this->dotPlaceholder, '__asterisk__'],
            ['.', '*'],
            $value
        );
    }

    protected function shouldStopFormatting($attribute)
    {
        return false;
    }

    /**
     * Get the value of a given attribute.
     *
     * @param  string  $attribute
     * @return mixed
     */
    protected function getValue($attribute)
    {
        if (Arr::has($this->formattedData, $attribute)) {
            return Arr::get($this->formattedData, $attribute);
        }
        return Arr::get($this->data, $attribute);
    }

    /**
     * @param string $attribute
     * @param mixed $value
     */
    protected function saveValue($attribute, $value)
    {
        Arr::set($this->formattedData, $attribute, $value);
    }

    /**
     * @param string $attribute
     * @param FormatterRuleContract $rule
     * @return mixed
     */
    protected function formatUsingCustomRule($attribute, $value, $rule)
    {
        $rule->setFormatter($this);

        return $rule->format($attribute, $value);
    }
}
