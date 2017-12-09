<?php
namespace SeanMorris\Form;
/**
 * Logic for Forms.
 */
class Form
{
	/**
	 * HTTP method to submit with.
	 */
	protected $method;

	/**
	 * URL to submit to.
	 */
	protected $action;

	/**
	 * Encoding type. Used for file uploads.
	 */
	protected $enctype;

	/**
	 * Theme to render the form with.
	 */
	protected $theme;

	/**
	 * List of child fields.
	 */
	protected $fields = [];

	/**
	 * Aggregated valdiation errors.
	 */
	protected $errors = [];

	/**
	 * Skeleton used to create form.
	 */
	protected $skeleton = [];

	/**
	 * Shorthand mappting of field types to classes.
	 */
	protected static
		$typesToClasses = [
			null => 'SeanMorris\Form\Field'
			, 'text' => 'SeanMorris\Form\Field'
			, 'password' => 'SeanMorris\Form\PasswordField'
			, 'hidden' => 'SeanMorris\Form\HiddenField'
			, 'textarea' => 'SeanMorris\Form\TextareaField'
			, 'fieldset' => 'SeanMorris\Form\Fieldset'
			, 'checkbox' => 'SeanMorris\Form\CheckBoxField'
			, 'radios' => 'SeanMorris\Form\RadiobuttonField'
			, 'file' => 'SeanMorris\Form\FileField'
			, 'html' => 'SeanMorris\Form\Html'
			, 'submit' => 'SeanMorris\Form\SubmitField'
			, 'button' => 'SeanMorris\Form\ButtonField'
			, 'select' => 'SeanMorris\Form\SelectField'
		]
	;
	
	/**
	 * Sets up the form.
	 * 
	 * @param array $skeleton Information to set up form fields.
	 */
	public function __construct(array $skeleton = [])
	{
		$this->skeleton = $skeleton;
		$this->method = 'GET';

		if(isset($skeleton['_method']))
		{
			$this->method = $skeleton['_method'];
		}

		$this->action = null;

		if(isset($skeleton['_action']))
		{
			$this->action = $skeleton['_action'];
		}

		if(isset($skeleton['_theme']))
		{
			$this->theme = $skeleton['_theme'];
		}

		$this->fields = static::processFieldDefs($skeleton);
	}

	/**
	 * Process a list of fieldDefs into a list of field objects.
	 * 
	 * @param $skeleton list of fieldDefs.
	 */
	public function processFieldDefs($skeleton)
	{
		$fields = [];

		foreach($skeleton as $fieldName => $fieldDef)
		{
			if(preg_match('/^_/', $fieldName))
			{
				continue;
			}

			$type = isset($fieldDef['type'])
				? $fieldDef['type']
				: null
			;

			$fieldClass = NULL;
			$curClass = get_called_class();

			$fieldClass = isset($fieldDef['_class'])
				? $fieldDef['_class']
				: null
			;

			if(!$fieldClass)
			{
				while($curClass)
				{
					if(isset($curClass::$typesToClasses[$type]))
					{
						$fieldClass = $curClass::$typesToClasses[$type];
						break;
					}

					$curClass = get_parent_class($curClass);
				}
			}

			if(!$fieldClass)
			{
				$fieldClass = 'SeanMorris\Form\Field';
			}

			$fieldDef['name'] = $fieldName;

			$field = new $fieldClass($fieldDef, $this);

			if(isset($fieldDef['value']))
			{
				$field->set($fieldDef['value']);
			}

			$fields[$fieldName] = $field;
		}

		return $fields;
	}

	/**
	 * Returns a list of values from this forms fields.
	 * 
	 * @return array List of values.
	 */
	public function getValues()
	{
		$fields = $this->fields;
		$values = [];

		foreach($fields as $fieldName => $field)
		{
			$fieldValue = $field->value();

			if($field->isArray() && is_array($fieldValue))
			{
				$values[$fieldName] = $fieldValue;
			}
			else if(is_array($fieldValue))
			{
				$values = array_merge($values, $fieldValue);
			}
			else
			{
				$values[$fieldName] = $fieldValue;
			}
		}

		return $values;
	}

	/**
	 * Sets values forthis forms fields.
	 * 
	 * @param array $values List of values.
	 * @param array $override Override locked fields.
	 */
	public function setValues(array $values = [], $override = false)
	{
		\SeanMorris\Ids\Log::debug(
			'Setting Values for FORM...'
			, $values
			, $override
		);
		$this->errors = [];

		if($values === NULL)
		{
			if($this->method === 'POST')
			{
				$files = array_map(
					function($file)
					{
						return (object)$file;
					}
					, $_FILES
				);

				$values = $_POST + $files;
			}
			elseif($this->method === 'GET')
			{
				$values = $_GET;
			}
		}

		$fields = $this->fields;

		foreach($values as $fieldName => $fieldValue)
		{
			if(isset($fields[$fieldName]))
			{
				$fields[$fieldName]->set($fieldValue, $override);
			}
		}

		foreach($fields as $fieldName => $field)
		{
			if($field instanceof Fieldset && !$field->isArray())
			{
				$field->set($values, $override);
			}
		}

		return $values;
	}

	/**
	 * Validates and sets values for this forms fields.
	 * 
	 * @param array $values List of values.
	 * 
	 * @return boolean TRUE if no errors were generated.
	 */
	public function validate(array $values = [])
	{
		$this->setValues($values);

		foreach($this->fields as $fieldName => $field)
		{
			if(!$field->validate())
			{
				$this->errors = array_merge($this->errors, $field->errors());
			}
		}

		return !$this->errors;
	}

	/**
	 * Returns a list of errors from validation.
	 * 
	 * @return array list of any validation errors generated.
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * Renders the form.
	 * 
	 * @param string $theme Classname of theme to use in rendering the form.
	 * 
	 * @return object View object for form.
	 */
	public function render($theme = NULL)
	{
		if(!$theme && $this->theme)
		{
			$theme = $this->theme;
		}

		if(!$theme)
		{
			$theme = 'SeanMorris\Form\Theme\Theme';
		}

		$fields = [];

		$this->enctype = 'multipart/form-data';

		foreach($this->fields as $field)
		{
			if($field instanceof FileField)
			{
				$this->enctype = 'multipart/form-data';
			}

			$fields[] = $field->render($theme);
		}

		$rendered = $theme::render($this, [
			'fields'     => $fields
			, 'method'   => $this->method
			, 'action'   => $this->action
			, 'enctype'  => $this->enctype
			, 'skeleton' => $this->skeleton
		]);

		return $rendered;
	}

	/**
	 * Return this form's list of field objects.
	 * 
	 * @return array List of field objects.
	 */
	public function fields()
	{
		return $this->fields;
	}
}