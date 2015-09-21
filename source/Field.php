<?php
namespace SeanMorris\Form;
class Field
{
	protected
		$name
		, $title
		, $form
		, $value
		, $superior
		, $type
		, $mute
		, $multi
		, $array
		, $locked
		, $fieldDef
		, $disabled
		, $options
		, $validators = []
		, $errors = []
	;
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

		if(isset($fieldDef['_mute']))
		{
			$this->mute = $fieldDef['_mute'];
		}

		if(isset($fieldDef['_options']))
		{
			$this->options = $fieldDef['_options'];
		}

		if(isset($fieldDef['_validators']))
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

	public function set($value, $override = false)
	{
		// var_dump($value);

		if($this->locked && !$override)
		{
			return;
		}

		$this->value = $value;
	}

	public function value(Form $form)
	{
		return $this->value;
	}

	public function validate()
	{
		foreach($this->validators as $validator)
		{
			if(!$validator->validate($this, $this->form))
			{
				$this->errors = array_merge($this->errors, $validator->errors());
			}

			foreach($this->errors as &$error)
			{
				$error = sprintf($error, $this->title);
			}
		}

		return !$this->errors;
	}

	public function errors()
	{
		return $this->errors;
	}

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

	public function suppress()
	{
		return false;
	}

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
		$attrs['value'] = $this->value;

		return $attrs;
	}

	public function render($theme)
	{
		$rendered = $theme::render($this, [
			'name' => $this->name
			, 'fullname' => $this->fullname()
			, 'type' => $this->type
			, 'value' => $this->value
			, 'title' => $this->title
			, 'mutli' => $this->multi
			, 'superior' => $this->superior
			, 'disabled' => $this->disabled
			, 'options' => $this->options
			, 'attrs' => $this->attrs()
		]);

		return $rendered;
	}

	public function superior()
	{
		return $this->superior;
	}

	public function isArray()
	{
		return false;
	}

	public function lock()
	{
		$this->locked = true;
	}

	protected function fullname()
	{
		$fullname = $this->name;
		
		if($this->superior && $this->superior->isArray())
		{
			$superior = $this->superior;

			/*
			$nameChain = [$fullname];
			while($superior)
			{
				if($superior->isMulti() && isset($this->delta))
				{
					$nameChain[] = $this->delta;
				}

				if($superior->isArray())
				{
					$nameChain[] = $superior->attr('name');
				}

				if($superior->superior())
				{
					$superior = $superior->superior();
				}
				else
				{
					$superior = false;
				}
			}

			$nameChain = array_reverse($nameChain);

			$root = array_shift($nameChain);

			$fullname = sprintf('%s[%s]', $root, implode('][', $nameChain));
			*/

			$fullname = $superior->fullname()
				. (isset($fullname)
					? '[' . $fullname . ']'
					: NULL
				);
		}

		return $fullname;
	}
}