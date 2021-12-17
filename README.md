# Laravel String Formatter Helper

This is package for helping dev process.
Clone from Laravel Validation

## Require

- laravel/framework: ^8.41

## Install 

Config repository composer.json 

```
{
    "type": "vcs",
    "url": "https://github.com/micro-php-libs/string-formatter.git"
}
```

Then add the package to dependency

```
"micro-php-libs/string-formatter": "dev-master",
```

## Snippets

**Custom a complex rule**

```
use MicroPhpLibs\StringFormatter\Rules\FormatterRule;

class Max100FormatRule extends FormatterRule
{
    public function format($attribute, $value)
    {
        if (strlen($value) > 100) {
            return substr($value, 0, 100);
        }

        return $value;
    }
}
```

**Sample using scripts** 

```
/** @var array */
$formatterRules = [
    'title' => 'trim|replace:Local Composer Dependencies,[Local Composer Dependencies]|replace:[Local Composer Dependencies],[Composer Dependencies]|limit:150',
    'publish_date' => 'date_format:Y-m-d',
    'title_md5' => 'trim|limit:150|md5',
    'short_description' => ['trim', new \App\Supports\Max100FormatRule()]
];

$data = [
    'title' => 'Developing Laravel Packages with Local Composer Dependencies',
    'publish_date' => '2021-09-09 10:10:00',
    'title_md5' => 'Developing Laravel Packages with Local Composer Dependencies',
    'short_description' => 'Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description '
];

echo "before: <br/>";

var_dump($data);

// TODO: add closure rule
$formatterFactory = new FormatterFactory();
$formatted = $formatterFactory->make($data, $formatterRules)->format()->formatted();

echo "<br/> after: <br/>";

var_dump($formatted);
die();
```

**Result**

```
before:
array(4) { ["title"]=> string(60) "Developing Laravel Packages with Local Composer Dependencies" ["publish_date"]=> string(19) "2021-09-09 10:10:00" ["title_md5"]=> string(60) "Developing Laravel Packages with Local Composer Dependencies" ["short_description"]=> string(308) "Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description Very long description " }
after:
array(4) { ["title"]=> string(56) "Developing Laravel Packages with [Composer Dependencies]" ["publish_date"]=> string(10) "2021-09-09" ["title_md5"]=> string(32) "75984d4d89c7cb439b8c4e55f224dabb" ["short_description"]=> string(100) "Very long description Very long description Very long description Very long description Very long de" }
```

## NOTE

- For my usage, if it can help you in someway then great.
- No promise solve your issue
- It opens then feel free to fork and fix or add your changes if needed
- No testing, not sure if it still has bugs

## License

Under MIT. 

Package by Ty Huynh <hongty.huynh@gmail.com>. 

Feel free to use.
