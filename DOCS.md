# SeanMorris\Form Documentation

The guide to the Form library.

## Form Arguments

### _method

Specify GET or POST to set the request type. Defaults to GET.

PUT and DELETE are valid but will not be supported by most browsers.

```php
$skeleton['_method'] = 'POST';
```
### _action

Set _action to submit the form to a URL other than the one it is served from.

```php
$skeleton['_action'] = '/submit/to/path';
```

### _theme

```php
$skeleton['_theme'] = 'Namespace\ThemeClass';
```

Set a theme to render the form and its fields.

## Field Types

Elements without a `type` key will render as text inputs, but it is still recommeneded to supply the type for text fields.

If necesary, see [EXTENDING](EXTENDING.md) if new fieldtypes need to be created.


```php
$skeleton['someString'] = [
	'_title' => 'String'
	, 'type' => 'text'
];
```

### Checkbox

Checkbox fields will simply render a checklist with a value of 1 when checked.

```php
$skeleton['image'] = [
	'_title' => 'Image'
	, 'type' => 'checkbox'
];
```

### FieldSet

Fieldsets contain other fields. 

```php
$children['childA'] = [
	'_title' => 'Child Field A'
	, 'type' => 'text',
];

$children['childB'] = [
	'_title' => 'Child Field B'
	, 'type' => 'text',
];

$skeleton['fieldset'] = [
	'_title' => 'Fields'
	, 'type' => 'fieldset'
	, '_children' => $children
];
```
#### Fieldsets as input arrays

Fieldsets can encapsulate their children with the `_array` key. In the following example, The value of `childA` will be submitted as `fieldset[childA]`:

```php
$children['childA'] = [
	'_title' => 'Child Field A'
	, 'type' => 'text',
];

$skeleton['fieldset'] = [
	'_title' => 'Fields'
	, 'type' => 'fieldset'
	, '_children' => $children
	, '_array' => TRUE
];
```

### File

Data can be retrieved from the file field by setting the form values like so: `$form->setValues($_FILES + $_POST);`

```php
$skeleton['image'] = [
	'_title' => 'Image'
	, 'type' => 'file'
];
```

### Hidden 

Hidden fields are passed to the browser but not rendered.

```php
$skeleton['id'] = [
	'type' => 'hidden'
];
```

### Password

Password type fields will never render their own value, for security reasons.

```php
$skeleton['password'] = [
	'_title' => 'Password'
	, 'type' => 'password'
];
```

### Radios

Password type fields will never render their own value, for security reasons.

```php
$skeleton['eyeColor'] = [
	'_title' => 'Eye Color'
	, 'type' => 'radios'
	, '_options' => [
		'blue' => 'Blue'
		, 'brown' => 'Brown'
		, 'green' => 'Green'
		, 'hazel' => 'Hazel'
	]
];
```

### Text Field

Text fields are simple. Attributes like `maxlength` or `autocomplete` can be specified.

```php
$skeleton['someString'] = [
	'_title' => 'String'
	, 'type' => 'text'
	, 'maxlength' => 120
	, 'autocomplete' => 'off'
];
```

### Textarea

Textarea fields are almost as simple as text fields. Attributes like `rows` and `cols` can be specified.

```php
$skeleton['someString'] = [
	'_title' => 'String'
	, 'type' => 'text'
	, 'rows' => 10
];
```

## Validators

### Email validator

The Email Validator take a single error string. If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['email'] = [
	'_title' => 'Email'
	, 'type' => 'text'
	, '_validators' => [
		'SeanMorris\Form\Validator\EmailValidator' => '%s must be a valid email.'
	]
];
```

### Range Validator

The Range Validator take an array of 3 keys, mapped to their error strings. The smaller numerical key is the minimum value of the input, the larger is the maxiumum. The string key 'nan' specifies the error to display when a non numerican value is submitted. If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['testField'] = [
  'type' => 'number'
  , '_title' => 'Test Field'
  , '_validators' => [
    'SeanMorris\Form\Validator\RangeValidator' => [
      0 => '%s must be at least 0.'
      , 10 => '%s must be no greater than 10.'
      , 'nan' => '%s must be a numberical value.'
    ]
  ]
];
```

### Regex Validator
Class `SeanMorris\Form\Validator\RegexValidator`

The Regex validator takes an array of error messages, keyed by regex patterns. If the input value doesn't match any pattern, its error will be raised. If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['testField'] = [
  'type' => 'text'
  , '_title' => 'Test Field'
  , '_validators' => [
    'SeanMorris\Form\Validator\RegexValidator' => [
      '/.{8,}/' => '%s must be at least 8 characters'
    ]
  ]
];
```

## Reusable Forms

You can extend the Form class to create a Reusable Form. You'll need to override the constructor to intercept the $skeleton. You can then build the form on the existing skeleton. You can use the skeleton array submitted to the constructor to allow fields to be added on the fly.

Just remember to call the parent constructor afterward.

```php
class ReusableForm extends \SeanMorris\Form\Form
{
	public function __construct($skeleton = [])
	{
		$skeleton['_method'] = 'POST';

		$skeleton['name'] = [
			'_title' => 'Name'
			, 'type' => 'text'
		];

		parent::__construct($skeleton);
	}
}


$form = new ReusableForm();
```
## More...

For the usage guide and field type list read [DOCS](DOCS.md).

For the guide to extending the library to create new field types, read [EXTENDING](EXTENDING.md).

For legal information, check [LICENSE](LICENSE) and [NOTICE](NOTICE).

[README](README.md)