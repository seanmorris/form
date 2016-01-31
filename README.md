# SeanMorris\Form

## Rapid, themable forms for PHP.

The form library allows you to rapidly develop, validate and style forms, so you can get back to programming.

# Creating a Form

```php
$form = new \SeanMorris\Form\Form([
  'testField' => [
    'type' => 'text'
    , '_title' => 'Test Field'
  ]
]);

# Validation

# Styling and theming