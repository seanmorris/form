<?php
namespace SeanMorris\Form;
/**
 * Logic for Fields.
 */
class Field
{
	/**
	 * Name of the field.
	 * @var string
	 */
	protected $name;

	/**
	 * Title of the field.
	 * @var string
	 */
	protected $title;

	/**
	 * Form object that contains the field.
	 * @var object
	 */
	protected $form;

	/**
	 * Value of the field.
	 * @var string
	 */
	protected $value;

	/**
	 * Fieldset of the field.
	 * @var object
	 */
	protected $superior;

	/**
	 * Type of the field.
	 * @var string
	 */
	protected $type;
	
	/**
	 * Field is multivalued if true.
	 * @var bool
	 */
	protected $multi;

	/**
	 * Field can hold an array if true.
	 * @var bool
	 */
	protected $array;

	/**
	 * Locked fields will not change value.
	 * @var bool
	 */
	protected $locked;

	/**
	 * FieldDef array that generates this field.
	 * @var array
	 */
	protected $fieldDef;

	/**
	 * If true, disables the field in the HTML.
	 * @var bool
	 */
	protected $disabled;

	/**
	 * Array of options for Radio buttons or Select fields.
	 * @var array
	 */
	protected $options;

	/**
	 * If true, the value can be submitted but not rendered.
	 * @var bool
	 */
	protected $suppress = FALSE;

	/**
	 * List of validator objects.
	 * @var array
	 */
	protected $validators = [];

	/**
	 * List of validation errors.
	 * @var array
	 */
	protected $errors = [];

	/**
	 * List of validation errors.
	 * @var array
	 */
	protected static
		$validatorShorthand = [
			'_required' => 'SeanMorris\Form\Validator\Required'
			, '_email' => 'SeanMorris\Form\Validator\Email'
			, '_range' => 'SeanMorris\Form\Validator\Range'
			, '_regex' => 'SeanMorris\Form\Validator\Regex'
			, '_confirm' => 'SeanMorris\Form\Validator\Confirm'
			, '_optionFilter' => 'SeanMorris\Form\Validator\OptionFilter'
		]
	;

	/**
	 * Sets up the field based on the $fieldDef
	 * 
	 * @param array $fieldDef Array describing field details.
	 * @param object $form Form that owns this field.
	 */
	public function __construct($fieldDef, $form)
	{
		if(isset($fieldDef['name']))
		{
			$this->name = $fieldDef['name'];	
		}
		
		if(isset($fieldDef['_title']))
		{
			$this->title = $fieldDef['_title'];;	
		}		
		
		if(isset($fieldDef['type']))
		{
			$this->type = $fieldDef['type'];	
		}

		if(isset($fieldDef['_lock']))
		{
			$this->locked = $fieldDef['_lock'];
		}

		if(isset($fieldDef['value']))
		{
			$this->value = $fieldDef['value'];	
		}

		if(isset($fieldDef['_multi']))
		{
			$this->multi = $fieldDef['_multi'];
		}

		if(isset($fieldDef['_suppress']))
		{
			$this->suppress = $fieldDef['_suppress'];
		}

		if(isset($fieldDef['_options']))
		{
			$this->options = $fieldDef['_options'];
		}

		$curClass = get_called_class();

		while($curClass)
		{
			foreach($curClass::$validatorShorthand as $key => $class)
			{
				if(isset($fieldDef[$key]) && !isset($fieldDef['_validators'][$class]))
				{
					$fieldDef['_validators'][$class] = $fieldDef[$key];
				}
			}

			$curClass = get_parent_class($curClass);
		}

		if(isset($fieldDef['_validators']) && is_array($fieldDef['_validators']))
		{
			foreach($fieldDef['_validators'] as $class => $args)
			{
				if(!is_a($class, 'SeanMorris\Form\Validator\validator', TRUE))
				{
					throw new \Exception(sprintf(
						'Bad validator suppplied: %s'
						, $class
					));
				}

				$this->validators[] = new $class($args);
			}
		}

		$this->fieldDef = $fieldDef;
		
		$this->form = $form;
	}

	/**
	 * Sets the field value
	 * 
	 * @param mixed $value field value.
	 * @param bool $override if true, the call will change the value of a locked field.
	 */
	public function set($value, $override = false)
	{
		\SeanMorris\Ids\Log::debug(
			sprintf('Setting value for FIELD[%s]...', $this->name)
			, $value
			, $override
		);
		$this->errors = [];

		if($this->locked && !$override)
		{
			return;
		}

		$this->value = $value;
	}

	/**
	 * Gets the field value
	 * 
	 * @return mixed $value field value.
	 */
	public function value()
	{
		return $this->value;
	}

	/**
	 * Loop over validators, run them and agregate errors.
	 */
	public function validate()
	{
		$this->errors = [];

		foreach($this->validators as $validator)
		{
			if(!$validator->validate($this->form, $this))
			{
				$this->errors = array_merge($this->errors, $validator->errors());
			}
		}

		foreach($this->errors as &$error)
		{
			$error = sprintf($error, $this->title);
		}

		return !$this->errors;
	}

	/**
	 * Return whether or not field is suppressed.
	 * 
	 * @return bool true if suppresdes.
	 */
	public function suppress()
	{
		return $this->suppress;
	}

	/**
	 * Return a list of validation errors.
	 * 
	 * @return array List of valiation errors.
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * Return a field attribute.
	 * 
	 * @return string attribute value.
	 */
	public function attr($name)
	{
		if($name == 'name')
		{
			return $this->name;
		}

		if($name == 'type')
		{
			return $this->type;
		}
	}

	/**
	 * Return a list of field attributes.
	 * 
	 * @return array of attributeName => value pairs.
	 */
	public function attrs()
	{
		$attrs = [];

		foreach($this->fieldDef as $k => $v)
		{
			if($k[0] == '_' || !is_scalar($v))
			{
				continue;
			}

			if($k[0] == '-')
			{
				$attrs['data' . $k] = $v;
				continue;
			}

			$attrs[$k] = $v;
		}

		$attrs['name'] = $this->fullname();
		//$attrs['value'] = $this->suppress ? NULL : $this->value;
		unset($attrs['value']);

		return $attrs;
	}

	/**
	 * Render the field.
	 * 
	 * @return object|string View or HTML for field.
	 */
	public function render($theme)
	{
		$rendered = $theme::render($this, [
			'name' => $this->name
			, 'fullname' => $this->fullname()
			, 'type' => $this->type
			, 'value' => $this->suppress ? NULL : $this->value
			, 'title' => $this->title
			, 'mutli' => $this->multi
			, 'superior' => $this->superior
			, 'disabled' => $this->disabled
			, 'options' => $this->options
			, 'attrs' => $this->attrs()
			, 'fieldDef' => $this->fieldDef
		]);

		return $rendered;
	}

	/**
	 * Returns the field title.
	 * 
	 * @return bool true if suppresdes.
	 */
	public function title()
	{
		return $this->title;
	}

	/**
	 * Returns the field's fieldset if applicable.
	 * 
	 * @return object the fieldset.
	 */
	public function superior()
	{
		return $this->superior;
	}

	/**
	 * Returns the field's fieldset if applicable.
	 * 
	 * @return bool true if suppresdes.
	 */
	public function isArray()
	{
		return false;
	}

	/**
	 * Returns the field's type
	 * 
	 * @return string The field's type.
	 */
	public function type()
	{
		return $this->type;
	}

	/**
	 * Locks the field.
	 */
	public function lock()
	{
		$this->locked = true;
	}

	/**
	 * Returns the field's full name.
	 * 
	 * @return string the full name.
	 */
	public function fullname()
	{
		$fullname = $this->name;
		
		if($this->superior && $this->superior->isArray())
		{
			if($fullname == '0' && $this->superior->multi == FALSE)
			{
				$fullname = NULL;
			}

			$superior = $this->superior;

			$fullname = $superior->fullname()
				. (isset($fullname)
					? '[' . $fullname . ']'
					: NULL
				);
		}

		return $fullname;
	}

	public function fieldDef()
	{
		return $this->fieldDef;
	}
}