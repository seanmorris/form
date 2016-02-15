# SeanMorris\Form

## Rapid, themable forms for PHP.

The form library allows you to rapidly develop, validate and style forms, so you can get back to programming.

# Creating a Form

Creating a form is simple. Elements starting with a letter or number are fields. Elements starting with underscores are special keys. For example `_method` allows you to set the choose a GET or POST request for your form.

Elements starting with a letter or number are rendered directly into the &lt;input&gt; or &lt;select&gt; tag. Elements beginning with an underscore are passed to logic but not rendered.

For example, the _title attribute generates a &lt;label&gt; tag for the form field.

```php
$skeleton['_method'] = 'POST';

$skeleton['testField'] = [
  'type' => 'text'
  , '_title' => 'Test Field'
];

$skeleton['submit'] = [
  '_title' => 'Submit'
  , 'type' => 'submit'
];

$form = new \SeanMorris\Form\Form($skeleton);

```

# Validation

Validators are specified on the _validators key. Its an array keyed by validator class. The values are arrays of arguments to pass to the constructor.

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

Filtering and validating submitted values is simple:


```php
$form->setValues($_POST);

if($form->validate())
{
  // Values will only contain keys for each of the fields.
  $values = $form->getValues();
}
else
{
  $errors = $form->erorrs();
}

```

## More...

For the usage guide and field type list read [DOCS](DOCS.md).

For the guide to extending the library to create new field types, read [EXTENDING](EXTENDING.md).

For legal information, check [LICENSE](LICENSE) and [NOTICE](NOTICE).

[README](README.md)