# Extending SeanMorris\Form

The examples in this document are included in the `test/` directory of the Form package.

## Creating reusable Forms

Creating a reusable form is simple. The first step is to create a class that extends `SeanMorris\Form\Form`. Then implement its constructor as in the example. The body of the constructor should add any necessary form arguments and fieldDefs to the skeleton. Note that it should take the provided skeleton rather than creating a new one. This allows additional fields to be added in individual instances. You can preprocess these fields in any way you like.

Once the skeleton is populated, passing it to a call to `parent::__construct()` will build the form.

```php
namespace SeanMorris\Form\Test\Extension;
class ProfileForm extends \SeanMorris\Form\Form
{
    protected static
        $typesToClasses = [
            'name' => 'SeanMorris\Form\Test\Extension\NameField'
            , 'height' => 'SeanMorris\Form\Test\Extension\HeightField'
        ];
        
    public function __construct($skeleton = [])
    {
        $skeleton['_method'] = 'POST';
        
        $skeleton['firstName'] = [
            '_title'      => 'First Name'
            , 'type'      => 'name'
            , '_required' => '%s is required'
        ];

        $skeleton['lastName'] = [
            '_title'      => 'Last Name'
            , 'type'      => 'name'
            , '_required' => '%s is required'
        ];

        $skeleton['bio'] = [
            '_title' => 'Bio'
            , 'type' => 'textarea'
        ];

        $skeleton['height'] = [
            '_title' => 'Height'
            , 'type' => 'height'
        ];

        $skeleton['submit'] = [
            'type' => 'Submit'
        ];

        parent::__construct($skeleton);
    }
}
```

## Creating new Fields

This example will use the `NameField` class, which is also used in the package test proceedure. The full source of the class is provided at the end of this section.

Creating a new field is very similar to creating a new form. Simply extend the `\SeanMorris\Form\Field` class, and implement the contstructor as below.

Build fieldDef in the constructor function, and pass it to parent::__construct. Take care to account for any fieldDef values passed in.

```php
namespace SeanMorris\Form\Test\Extension;
class NameField extends \SeanMorris\Form\Field
{
    protected static
        $validatorShorthand = [
            '_name' => 'SeanMorris\Form\Test\Extension\NameValidator'
        ];

    public function __construct($fieldDef, $form)
    {
        $fieldDef += [
            'type'  => 'text'
            , '_name' => '%s must consist of only letters.' 
        ];

        parent::__construct($fieldDef, $form);
    }
}

```

### Creating compound Fields

Compund fields are just a smart word for a fieldgroup that comes with fields already inside. Creating one is the same as creating any other kind of field, just start by extending the `\SeanMorris\Form\Fieldset` class, then build the child fieldDefs on the skeleton in the constructor. You can set the `_array` key to TRUE on the main fieldDef to group all the values into an array in the submitted data.

```php
namespace SeanMorris\Form\Test\Extension;
class HeightField extends \SeanMorris\Form\Fieldset
{
    public function __construct($fieldDef, $form)
    {
        $children['units'] = [
            'type'     => 'text'
            , '_title' => 'Units'
            , '_regex' => [
                '/^\d+$/' => '%s must consist of only numbers.'
            ]
        ];

        $children['measure'] = [
            'type'       => 'select'
            , '_title'   => 'Measure'
            , '_options' => [
                'Inches'        => 'in'
                , 'Centimeters' => 'cm'
            ]
        ];

        $fieldDef += [
            '_title'      => 'Height'
            , 'type'      => 'fieldset'
            , '_children' => $children
            , '_array'    => TRUE
        ];

        parent::__construct($fieldDef, $form);
    }
}
```

### Using the new Field in a Form

The simplest way to begin using the new field in a form is to set the `_class` key of a fieldDef, like so:

```php
$skeleton['firstName'] = [
    '_type'    => 'text'
    , 'title'  => 'firstName'
    , '_class' => 'SeanMorris\Form\Test\Extension\NameField'
];

$form = new \SeanMorris\Form\Form($skeleton);
```

### Shorthand mappings from Field types to Field classes

You can create shorthand mappings on your Form classes to allow fields to be invoked by type. Just populate the static property `$typesToClasses` with `type => fieldClassname` pairs.

You can also extend already extended forms without obliterating these settings, so you don't need to worry about copying parent class values into child classes. When assebling the form objects, if a class does not know how to map a type to a field class, it will check its parent. If that class cannot map the type to a field class, its parent will be checked. If it makes it all the way to the base class without successfully finding a mapping, the base `Field` class will be used to render the form.

For example, take a look at the mappings on `ProfileForm`.

```php
namespace SeanMorris\Form\Test\Extension;
class ProfileForm extends \SeanMorris\Form\Form
{
    protected static
        $typesToClasses = [
            'name' => 'SeanMorris\Form\Test\Extension\NameField'
            , 'height' => 'SeanMorris\Form\Test\Extension\HeightField'
        ];
```

This allows the field to be used simply by specifying the type key, like so:

```php
 $skeleton['firstName'] = [
    '_title'      => 'First Name'
    , 'type'      => 'name'
    , '_required' => '%s is required'
];

$form = new \SeanMorris\Form\Test\Extension\ProfileForm($skeleton);
```

## Creating new validators

New validators can be created by extending the `\SeanMorris\Form\Validator\Validator` class. Each constructor is different, so you'll need to implement your own logic to inject the parameter passed in.  The parameter just specifies information up the vaidator and does not include the field value.

For example, the `Required` validator takes the error message specified for it in the field. The `Regex` validator will accept a list of error messages, keyed by regular expressions. Confirm take a list of error messages keyed by field names.

The validate function accepts two params, `$form` and `$field`. `$form` is the main form, `$field` is the field being validated. Field is optional because validators are planned that will work on entire forms instead of specific fields.

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

### Shorthand mappings from fieldDef keys to Validator classes

Mapping fieldDef keys to new validators is similar to the mapping of skeleton keys to field classes, except it happens on the field level. Just extend the base `SeanMorris\Form\Field` class and add entries to the static property `$validatorShorthand`.

Checks will ascend through the class hierarchy in the same way as checks for new fields ascend through the `Form` class hierarchy.

```php
namespace SeanMorris\Form\Test\Extension;
class NameField extends \SeanMorris\Form\Field
{
    protected static
        $validatorShorthand = [
            '_name' => 'SeanMorris\Form\Test\Extension\NameValidator'
        ];
```

## Theming

Creating custom templates and themes is simple. You can theme the existing form and field classes, as well as any of their subclasses.

### Forms

In this example, the `ExtendedForm` View provides an overridden preprocessor as well as a new template. Both of these are optional when extending the `Form` View, but normally you'll want to implement one or the other. Leaving both out would give you a subclassed View that is essentially identical to its parent view.

The original template is made available through the call to `static::render($vars, 1);`. The first parameter contains the `$vars` from the preprocessor. The second parameter is ESSENTIAL in telling the render function to ascend through the View class hierarchy. Passing a 1 acsends to the parent View, passing 2 would ascend to the grandparent and so on.

```php
namespace SeanMorris\Form\Test\Extension\Theme;
class ExtendedForm extends \SeanMorris\Form\Theme\Form
{
    public function preprocess(&$vars)
    {
        parent::preprocess($vars);
        $vars['classes'] = [];
        if(isset($vars['skeleton']['_classes']) && is_array($vars['skeleton']['_classes']))
        {
            $vars['classes'] = $vars['skeleton']['_classes'];
        }
    }
}
__halt_compiler(); ?>
<div class = "extendedForm<?php
    foreach($classes as $class):?> <?=$class;?><?php endforeach;?>">
    <?php print static::render($vars, 1);?>
</div>
```

### Fields

Creating templates for Fields is very similar to that of Forms. Preprocessors and template overrides follow all the same rules.

Please note that that this preproccesing logic is for testing/example purposes only, a production environment should implement the validation, storage and loading of files, rather than loading it directly from /tmp and including it in the HTML source as seen below.

```php
namespace SeanMorris\Form\Test\Extension\Theme;
class AvatarField extends \SeanMorris\Form\Theme\FileField
{   
    public function preprocess(&$vars)
    {
        $vars['attrs']['type'] = 'file';
        $vars['src'] = NULL;
        if(isset($vars['value'], $vars['value']->tmp_name))
        {
            $vars['src'] = 'data:image/png;base64,' . base64_encode(
                file_get_contents($vars['value']->tmp_name)
            );
        }
    }
}
__halt_compiler(); ?>
<?php print static::render($vars, 1); ?>
<?php if($src):?>
<img src = "<?=$src;?>" />
<?php endif; ?>
```

### Putting it all together

Once you've created your templates, you can group them into a Theme to use in rendering the form.

To do so, extend the `Form` package `Theme` class, and populate the static `$view` property with a list of key => value pairs mapping Form and/or Field classes to their View classes.

```php
namespace SeanMorris\Form\Test\Extension\Theme;
class ExtendedFormTheme extends \SeanMorris\Form\Theme\Theme
{
    protected static $view = [
        'SeanMorris\Form\Test\Extension\ProfileForm'
            => 'SeanMorris\Form\Test\Extension\Theme\ExtendedForm'
        , 'SeanMorris\Form\Test\Extension\AvatarField'
            => 'SeanMorris\Form\Test\Extension\Theme\AvatarField'
    ];
}
```
### Adding your form theme to an existing theme

To simplify themeing, you can allow one theme to delegate rendering to another. Just add the classname to the $theme array that you'd like to serve as the delegator.

To visualize this situation, Theme A can delegate rendering to Theme B if it does not have a View to render a given object. This allows you to include Form themes in the normal theme used throughout your site, without needing to clutter up the main theme or its class hierarchy.

```php
namespace SeanMorris\Form\Test\Extension\Theme;
class SomeOtherTheme extends \SeanMorris\Theme\Theme
{
    protected static $themes = [
        'SeanMorris\Form\Test\Extension\Theme\ExtendedFormTheme'
    ];
}
```

## More...

For the field type list, validator list, and usage guide read [DOCS](DOCS.md).

For the guide to extending the library to create new field types, read [EXTENDING](EXTENDING.md).

For legal information, check [LICENSE](LICENSE) and [NOTICE](NOTICE).

[README](README.md)