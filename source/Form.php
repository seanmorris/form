<?php
namespace SeanMorris\Form;
class Form
{
	protected 
		$method
		, $action
		, $enctype
		, $theme
		, $fields = []
		, $errors = []
	;

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
	
	public function __construct($skeleton)
	{
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

	public function processFieldDefs($skeleton, $array = false)
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

			if($array)
			{
				$fieldName .= '[]';
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

	public function getValues(Fieldset $fieldset = null)
	{
		$fields = $this->fields;
		$values = [];

		if($fieldset)
		{
			$fields = $fieldset->fields();
		}

		foreach($fields as $fieldName => $field)
		{
			$fieldValue = $field->value($this);

			if($fieldset && $fieldset->isMulti() && $fieldName == -1)
			{
				continue;
			}

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

	public function setValues(array $values = NULL, $override = false)
	{
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
				
				$values = $files + $_POST;
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
				$field->set($values, false);
			}
		}

		return $values;
	}

	public function validate(array $values = null)
	{
		$this->setValues($values, true);

		foreach($this->fields as $fieldName => $field)
		{
			if(!$field->validate())
			{
				$this->errors = array_merge($this->errors, $field->errors());
			}
		}

		return !$this->errors;
	}

	public function errors()
	{
		return $this->errors;
	}

	public function render($theme = NULL)
	{
		if(!$theme && $this->theme)
		{
			$theme = $this->theme;
		}

		if(!$theme)
		{
			$theme = 'SeanMorris\Form\Theme\Form\Theme';
		}

		$fields = [];

		foreach($this->fields as $field)
		{
			if($field instanceof FileField)
			{
				$this->enctype = 'multipart/form-data';
			}

			$fields[] = $field->render($theme);
		}

		$rendered = $theme::render($this, [
			'fields' => $fields
			, 'method' => $this->method
			, 'action' => $this->action
			, 'enctype' => $this->enctype
		]);

		return $rendered;
	}

	public function fields()
	{
		return $this->fields;
	}
}