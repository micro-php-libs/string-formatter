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

use MicroPhpLibs\StringFormatter\Rules\FormatterRuleContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class FormatterRuleParser
 * @ref https://github.com/illuminate/validation/blob/master/ValidationRuleParser.php
 */
class FormatterRuleParser
{
    public $data;

    // TODO: support implicit attributes
    public $implicitAttributes;

    /**
     * Create a new formatter rule parser.
     *
     * @param  array  $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Parse the human-friendly rules into a full rules array for the validator.
     *
     * @param  array  $rules
     * @return \stdClass
     */
    public function explode($rules)
    {
        $this->implicitAttributes = [];

        $rules = $this->explodeRules($rules);

        return (object) [
            'rules' => $rules,
            'implicitAttributes' => $this->implicitAttributes,
        ];
    }

    /**
     * Explode the rules into an array of explicit rules.
     *
     * @param  array  $rules
     * @return array
     */
    protected function explodeRules($rules)
    {
        foreach ($rules as $key => $rule) {
            // TODO: support wild card rules string
            $rules[$key] = $this->explodeExplicitRule($rule);
        }

        return $rules;
    }

    /**
     * Explode the explicit rule into an array if necessary.
     *
     * @param  mixed  $rule
     * @return array
     */
    protected function explodeExplicitRule($rule)
    {
        if (is_string($rule)) {
            return explode('|', $rule);
        }

        if (is_array($rule)) {
            return $rule;
        }

        return [];
    }

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param  array|string  $rules
     * @return array
     */
    public static function parse($rules)
    {
        if ($rules instanceof FormatterRuleContract) {
            return [$rules, []];
        }

        if (is_array($rules)) {
            $rules = static::parseArrayRule($rules);
        } else {
            $rules = static::parseStringRule($rules);
        }

        $rules[0] = static::normalizeRule($rules[0]);

        return $rules;
    }

    /**
     * Parse an array based rule.
     *
     * @param  array  $rules
     * @return array
     */
    protected static function parseArrayRule(array $rules)
    {
        return [Str::studly(trim(Arr::get($rules, 0))), array_slice($rules, 1)];
    }

    /**
     * Parse a string based rule.
     *
     * @param  string  $rules
     * @return array
     */
    protected static function parseStringRule($rules)
    {
        $parameters = [];

        // The format for specifying validation rules and parameters follows an
        // easy {rule}:{parameters} formatting convention. For instance the
        // rule "Max:3" states that the value may only be three letters.
        if (strpos($rules, ':') !== false) {
            [$rules, $parameter] = explode(':', $rules, 2);

            $parameters = static::parseParameters($rules, $parameter);
        }

        // NOTE: studly convert snake_case to SnakeCase with caps
        return [Str::studly(trim($rules)), $parameters];
    }

    /**
     * Parse a parameter list.
     *
     * @param  string  $rule
     * @param  string  $parameter
     * @return array
     */
    protected static function parseParameters($rule, $parameter)
    {
        $rule = strtolower($rule);

        if (in_array($rule, ['regex', 'not_regex', 'notregex'], true)) {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }

    /**
     * Normalizes a rule so that we can accept short types.
     *
     * @param  string  $rule
     * @return string
     */
    protected static function normalizeRule($rule)
    {
        switch ($rule) {
            case 'Int':
                return 'Integer';
            case 'Bool':
                return 'Boolean';
            default:
                return $rule;
        }
    }
}
