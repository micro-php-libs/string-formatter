<?php

/**
 * This file is part of DBCSoft Standard Package
 *
 * (c) Ty Huynh <hongty.huynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MicroPhpLibs\RavelFormatter\Concerns;

use DateTimeInterface;
use Illuminate\Support\Str;

trait FormatAttributes
{
    /**
     * if strict is true then return true/false other else return 1/0
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool|int
     */
    function formatBoolean($attribute, $value, $parameters = [])
    {
        $strict = true;

        if ($parameters && is_array($parameters) && count($parameters) > 0) {
            $trueValues = [true, 'yes', 'ok', 1, '1'];
            $strict = in_array($parameters[0], $trueValues, true);
        }

        return $strict ? (bool) $value : intval((bool) $value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return bool|int
     */
    function formatInteger($attribute, $value)
    {
        return intval($value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatSubstring($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(2, $parameters, 'substring');
        if (!$this->checkValidParameterCount(2, $parameters, 'substring')) {
            return false;
        }

        if (is_int($parameters[0])) {
            $from = (int) $parameters[0];
            $length = (int) $parameters[1];
            return Str::substr($value, $from, $length);
        } else {
            $from_s = $parameters[0];
            $to_s = $parameters[1];
            $start = 0;
            $end = strlen($value);
            if (!empty($from_s)) {
                $start = strpos($value, $from_s);
            }
            if ($start == -1) {
                $start = 0;
            }
            if (!empty($from_s)) {
                $end = strpos($value, $to_s);
            }
            if ($end == -1) {
                $end = strlen($value);
            }
            return substr($value, $start + strlen($from_s), $end - $start);
        }
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatTrim($attribute, $value, $parameters)
    {
        $charlist = " \t\n\r\0\x0B";

        if ($parameters && is_array($parameters) && count($parameters) > 0) {
            $charlist = implode("", $parameters);
        }

        return trim($value, $charlist);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatTrimStart($attribute, $value, $parameters)
    {
        $charlist = " \t\n\r\0\x0B";

        if ($parameters && is_array($parameters) && count($parameters) > 0) {
            $charlist = implode("", $parameters);
        }

        return ltrim($value, $charlist);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatTrimEnd($attribute, $value, $parameters)
    {
        $charlist = " \t\n\r\0\x0B";

        if ($parameters && is_array($parameters) && count($parameters) > 0) {
            $charlist = implode("", $parameters);
        }

        return rtrim($value, $charlist);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatReplace($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(2, $parameters, 'replace');
        if (!$this->checkValidParameterCount(2, $parameters, 'replace')) {
            return false;
        }

        $search = $parameters[0];
        $replace = $parameters[1];

        return str_ireplace($search, $replace, $value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatReplaceFirst($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(2, $parameters, 'replace_first');
        if (!$this->checkValidParameterCount(2, $parameters, 'replace_first')) {
            return false;
        }

        $search = $parameters[0];
        $replace = $parameters[1];

        return Str::replaceFirst($search, $replace, $value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatReplaceLast($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(2, $parameters, 'replace_last');
        if (!$this->checkValidParameterCount(2, $parameters, 'replace_last')) {
            return false;
        }

        $search = $parameters[0];
        $replace = $parameters[1];

        return Str::replaceLast($search, $replace, $value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    function formatStudly($attribute, $value)
    {
        return Str::studly($value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    function formatTitle($attribute, $value)
    {
        return Str::title($value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    function formatUpper($attribute, $value)
    {
        return Str::upper($value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    function formatLower($attribute, $value)
    {
        return Str::lower($value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    function formatMd5($attribute, $value)
    {
        return md5($value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    function formatSlug($attribute, $value)
    {
        return Str::slug($value);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool|null|string
     */
    function formatUrlParam($attribute, $value, $parameters)
    {
        if (!$this->checkValidParameterCount(1, $parameters, 'url_param')) {
            return false;
        }

        // param name
        $name = $parameters[0];

        // Use parse_url() function to parse the URL
        // and return an associative array which
        // contains its various components
        $url_components = parse_url($value);

        // Use parse_str() function to parse the
        // string passed via URL
        parse_str($url_components['query'], $params);

        // Display result
        return $params[$name] ?? null;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatLimit($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(1, $parameters, 'limit');
        if (!$this->checkValidParameterCount(1, $parameters, 'limit')) {
            return false;
        }

        $number = (int) $parameters[0];
        $end = isset($parameters[1]) && $parameters[1] ? $parameters[1] : '...';

        return Str::limit($value, $number, $end);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatWords($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(1, $parameters, 'words');
        if (!$this->checkValidParameterCount(1, $parameters, 'words')) {
            return false;
        }

        $number = (int) $parameters[0];
        $end = isset($parameters[1]) && $parameters[1] ? $parameters[1] : '...';

        return Str::words($value, $number, $end);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatPad($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(1, $parameters, 'pad');
        if (!$this->checkValidParameterCount(1, $parameters, 'pad')) {
            return false;
        }

        $length = (int) $parameters[0];
        $pad = isset($parameters[1]) && $parameters[1] ? $parameters[1] : ' ';

        return Str::padBoth($value, $length, $pad);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatPadLeft($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(1, $parameters, 'pad_left');
        if (!$this->checkValidParameterCount(1, $parameters, 'pad_left')) {
            return false;
        }

        $length = (int) $parameters[0];
        $pad = isset($parameters[1]) && $parameters[1] ? $parameters[1] : ' ';

        return Str::padLeft($value, $length, $pad);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return string
     */
    function formatPadRight($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(1, $parameters, 'pad_right');
        if (!$this->checkValidParameterCount(1, $parameters, 'pad_right')) {
            return false;
        }

        $length = (int) $parameters[0];
        $pad = isset($parameters[1]) && $parameters[1] ? $parameters[1] : ' ';

        return Str::padRight($value, $length, $pad);
    }

    /**
     * Format that an attribute to a valid date.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function formatDate($attribute, $value)
    {
        if ($value instanceof DateTimeInterface) {
            return true;
        }

        if ((! is_string($value) && ! is_numeric($value)) || strtotime($value) === false) {
            return false;
        }

        $date = date_parse($value);

        if (!checkdate($date['month'], $date['day'], $date['year'])) {
            return false;
        }

        return $date['year'] . '-' . $date['month'] . '-' . $date['day'];
    }

    /**
     * Format that an to a timestamp.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function formatTimestamp($attribute, $value)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }
        return strtotime($value);
    }

    /**
     * Format that an attribute to a date format.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function formatDateFormat($attribute, $value, $parameters)
    {
        //$this->requireParameterCount(1, $parameters, 'date_format');
        if (!$this->checkValidParameterCount(1, $parameters, 'date_format')) {
            return false;
        }

        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        $format = $parameters[0];

        return date($format, strtotime($value));
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool|null|string
     * //https://via.placeholder.com/150/000000/FFFFFF/?text=news-img.net
     */
    function formatUrlThumbnail($attribute, $value, $parameters)
    {
        $thumbnail = '';
        if (count($parameters) > 0) {
            $thumbnail = $parameters[0];
        }
        try {
            if (mh_is_valid_start_url($value)) {
                // FIXME: hardcode Timeout setting for here
                $getThumbnail = mh_thumbnail_meta_from_url($value, true, 5, false);
                $thumbnail = $getThumbnail ?? $thumbnail;
            }
        } catch (\Exception $e) {
        }
        return $thumbnail;
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param  int  $count
     * @param  array  $parameters
     * @param  string  $rule
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new \InvalidArgumentException("Formatter rule $rule requires at least $count parameters.");
        }
    }

    /**
     * Check a certain number of parameters to be present.
     *
     * @param  int  $count
     * @param  array  $parameters
     * @param  string  $rule
     * @return bool
     *
     */
    public function checkValidParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            return false;
        }
        return true;
    }
}
