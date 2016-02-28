# SeanMorris\Form

## Rapid, themable forms for PHP.

The Form library allows you to rapidly develop, theme, and validate forms, so you can get back to programming.

[![Build Status](https://travis-ci.org/seanmorris/form.svg?branch=master)](https://travis-ci.org/seanmorris/form) [![Latest Stable Version](https://poser.pugx.org/seanmorris/form/v/stable)](https://packagist.org/packages/seanmorris/form) [![Total Downloads](https://poser.pugx.org/seanmorris/form/downloads)](https://packagist.org/packages/seanmorris/form) [![Latest Unstable Version](https://poser.pugx.org/seanmorris/form/v/unstable)](https://packagist.org/packages/seanmorris/form) [![License](https://poser.pugx.org/seanmorris/form/license)](https://packagist.org/packages/seanmorris/form)

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

echo $form->render();

```

# Validation

Validators are specified on the _validators key. Its an array keyed by validator class. The values are arrays of arguments to pass to the constructor.

```php
$skeleton['testField'] = [
  'type' => 'text'
  , '_title' => 'Test Field'
  , '_validators' => [
    'SeanMorris\Form\Validator\Regex' => [
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

For the field type list, validator list, and usage guide read [DOCS](DOCS.md).

For the guide to extending the library to create new field types, read [EXTENDING](EXTENDING.md).

For legal information, check [LICENSE](LICENSE) and [NOTICE](NOTICE).

[README](README.md)
