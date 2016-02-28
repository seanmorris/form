# SeanMorris\Form Documentation

The guide to the Form library.

## Usage

Forms are build from arrays, called skeletons. They provide information about the fields of the form as well as the form itself. Pass the skeleton to the constructor, then call `render()` on the form object to render the form HTML.

"Skeletons" can contain "form arguments" which are string values with keys that begin with an underscore. They can also contain "fieldDefs" which define a field, are array values and have keys that begin with letters.

For example, the following form has a title textbox, a body textarea and a submit button. It will submit via POST.

```php
$skeleton['_method'] = 'POST';

$skeleton['title'] = [
    '_title' => 'Title'
    , 'type' => 'text'
];

$skeleton['body'] = [
    '_title' => 'Body'
    , 'type' => 'textarea'
];

$skeleton['submit'] = [
    '_title' => 'Submit'
    , 'type' => 'submit'
];

$form = new \SeanMorris\Form\Form($skeleton);

echo $form->render();

```

Forms can be populated with both the `setValues()` and `validate()` methods. Pass the coresponding array of input values into one or the other to fill values with user data.

Calling `validate()` will also populate the forms errors if there are any validators attached to the fields. `validate()` itself returns booleans, but the `errors()` method can be called to get the list of errors generated.

## Autopopulation

If no value is provided to the `setValues` or `validate` method call, then the form will populate itself from $_GET or $_POST, depending on which method it is set to use (node: this defaults to GET).

```php
if(!$form->validate($_POST))
{
    $errors = $form->errors();
}
```

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

Set a theme to render the form and its fields. If its not supplied, `SeanMorris\Form\Theme\Theme` will be used.

A theme class name can also be passed as the first param to the `render()` method, which will override the value provided in the skeleton.


```php
$skeleton['_theme'] = 'Namespace\ThemeClass';
```

-or-

```php
$form->render('Namespace\ThemeClass');
```

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

Checkbox fields will simply render a checkbox with a value of 1 when checked.

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
    '_title'      => 'Fields'
    , 'type'      => 'fieldset'
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
    '_title'      => 'Fields'
    , 'type'      => 'fieldset'
    , '_children' => $children
    , '_array'    => TRUE
];
```

### File

```php
$skeleton['image'] = [
    '_title' => 'Image'
    , 'type' => 'file'
];
```

File fields will be populated with `stdClass` type values when submitted. The object will have the following format:

```
stdClass Object (
    [name] => elemental.jpg
    [type] => image/jpeg
    [tmp_name] => /tmp/php5QegRw
    [error] => 0
    [size] => 76521
)
```

### Hidden 

Hidden fields are passed to the browser but not rendered.

```php
$skeleton['id'] = [
    'type'    => 'hidden'
    , 'value' => $someVar
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

Radio button fields take the special `_options` key. It is an associative array of keys to values. Keys are the actual values submitted, and values are displayed to the user.

```php
$skeleton['eyeColor'] = [
    '_title'     => 'Eye Color'
    , 'type'     => 'radios'
    , '_options' => [
        'blue' => 'Blue'
        , 'brown' => 'Brown'
        , 'green' => 'Green'
        , 'hazel' => 'Hazel'
    ]
];
```

### Select

Select fields work almost the same way as field of the `radios` type. You can also use the `multiple` key (recommended value of which is also `"multiple"`)    to automatically append a `[]` to the submitted name, and allow the user to select multiple values.

```php
$skeleton['eyeColor'] = [
    '_title'     => 'Eye Color'
    , 'type'     => 'Select'
    , '_options' => [
        'blue' => 'Blue'
        , 'brown' => 'Brown'
        , 'green' => 'Green'
        , 'hazel' => 'Hazel'
    ]
];
```

### Text Field

Text fields are simple. Attributes like `maxlength` or `autocomplete` can be specified as keys.

```php
$skeleton['someString'] = [
    '_title'         => 'String'
    , 'type'         => 'text'
    , 'maxlength'    => 120
    , 'autocomplete' => 'off'
];
```

### Textarea

Textarea fields are almost as simple as text fields. Attributes like `rows` and `cols` can be specified as keys.

```php
$skeleton['someString'] = [
    '_title' => 'String'
    , 'type' => 'text'
    , 'rows' => 10
];
```

### Aditional Fields.

New fields can be created by extending the base class `SeanMorris\Form\Field` The `_class` key takes class name to use for the field. If you want to render the field with a custom template, see [EXTENDING](EXTENDING.md) for advanced topics.

All you need to do is build the field's definition like you would as an element of a skeleton, and pass it on to `parent::__construct($fieldDef, $form);`.

```php
namespace SeanMorris\Form\Test\Extension;
class NameField extends \SeanMorris\Form\Field
{
    public function __construct($fieldDef, $form)
    {
        $fieldDef += [
            'type'     => 'text'
            , '_regex' => [
                '/^[a-zA-Z]$/' => '%s must consist of only letters.' 
            ]
        ];

        parent::__construct($fieldDef, $form);
    }
}
````
Usage:

```php
$skeleton['fieldName'] = [
    '_title'   => 'First Name'
    , '_class' => 'SeanMorris\Form\Test\Extension\NameField'
    , 'rows'   => 10
];
```

## Validators

Validation is simple. There are a few validators that can be used with some special field skeleton keys.

### Required validator

Special key: `_required`.

The required validator simply take an error message, to be displayed when the field is not filled in.    If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['title'] = [
    '_title'      => 'Title'
    , 'type'      => 'text'
    , '_required' => '%s - Required.'
];
```

### Email validator

Special key: `_email`.

The Email Validator take a single error string. If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['email'] = [
    '_title'   => 'Email'
    , 'type'   => 'text'
    , '_email' => '%s must be a valid email.'
];
```

### Range Validator

Special key: `_range`.

The Range Validator take an array of 3 keys, mapped to their error strings. The smaller numerical key is the minimum value of the input, the larger is the maxiumum. The string key 'nan' specifies the error to display when a non numerican value is submitted. If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['testField'] = [
    'type' => 'number'
    , '_title' => 'Test Field'
    , '_range' => [
        0       => '%s must be at least 0.'
        , 10    => '%s must be no greater than 10.'
        , 'nan' => '%s must be a numberical value.'
    ]
];
```

### Regex Validator
Class `SeanMorris\Form\Validator\Regex`

Special key: `_regex`.

The Regex validator takes an array of error messages, keyed by regex patterns. If the input value doesn't match any pattern, its error will be raised. If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['password'] = [
    'type'       => 'password'
    , '_title'   => 'Password'
    , '_confirm' => [
        'confirmPassword' => '%s and %s must match.'
    ]
];

$skeleton['confirmPassword'] = [
    'type'     => 'password'
    , '_title' => 'Confirm Password'
];
```
### Confirm Validator
Class `SeanMorris\Form\Validator\Confirm`

Special key: `_confirm`.

The confirm validator take an array of error messages keyed by field name. The keys refer to other fields that must be submitted with the same value as the main field, as in a password confirmation field. Multiple fields may be specified as confirmation fields, for extra certainty.

If `%1$s` appears in the message, it will be replaced with the field title, and if `%2$s` appears in the message, it will be replaced with the confirmation field title. If simply `%s` is used, the first `%s` will be replaced with the main field title, and the second `%s` with the confirmation field's.

```php
$skeleton['testField'] = [
    'type'       => 'password'
    , '_title'   => 'Password'
    , '_confirm' => [
         '/.{8,}/' => '%s must be at least 8 characters'
    ]
];
```

### OptionFilter Validator
Class `SeanMorris\Form\Validator\OptionFilter`

Special key: `_optionFilter`.

The OptionsFilter validator ensures radio/select fields are not filled with anything except the provided values. The validator take an error message to be displayed when the field does not have a valid value. If `%s` or `%1$s` appears in the message, it will be replaced with the field title.

```php
$skeleton['select'] = [
    'type'            => 'radios'
    , '_title'        => 'Options'
    , '_options'      => [
        'option_1' => 1
        , 'option_2' => 2
        , 'option_3' => 3
    ]
    , '_optionFilter' => 'Invalid value for %s.'
];

```

### Aditional validators.

New validators can be created by extending the base class `SeanMorris\Form\Validator\Validator` The `_validators` key takes an array keyed by validator class names. The values are passed to the validator constructors as arguments. See [EXTENDING](EXTENDING.md) for more information on customization.

```php
namespace SeanMorris\Form\Test\Extension;
class NameValidator extends \SeanMorris\Form\Validator\Validator
{
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function validate($form, $field = NULL)
    {
        parent::validate($form, $field);
        
        $value = $field->value();

        if(isset($value) && strlen($value) && !preg_match('/^[A-Za-z]+$/', $value))
        {
            $this->errors[] = $this->errorMessage;
        }

        return !$this->errors;
    }
}
```
Usage:

```php
$skeleton['name'] = [
    '_title'        => 'Name'
    , 'type'        => 'text'
    , '_validators' => [
        'SeanMorris\Form\Test\Extension\NameValidator' => '%s must contain only letters.'
    ]
];
```

## Reusable Forms

You can extend the Form class to create a Reusable Form. You'll need to override the constructor to intercept the $skeleton. You can then build the form on the existing skeleton. You can use the skeleton array submitted to the constructor to allow fields to be added on the fly. See [EXTENDING](EXTENDING.md) for more information on customization.

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

For the field type list, validator list, and usage guide read [DOCS](DOCS.md).

For the guide to extending the library to create new field types, read [EXTENDING](EXTENDING.md).

For legal information, check [LICENSE](LICENSE) and [NOTICE](NOTICE).

[README](README.md)
