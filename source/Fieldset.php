<?php
namespace SeanMorris\Form;
class Fieldset extends Field
{
	protected
		$title
		, $array
		, $multi
		, $delta
		, $values = []
		, $disabled
		, $cardinality
		, $fieldDef
		, $children = []
	;

	public function __construct($fieldDef, $form)
	{
		if(isset($fieldDef['_array']))
		{
			$this->array = $fieldDef['_array'];
		}

		if(isset($fieldDef['_multi']) && $fieldDef['_multi'])
		{
			$fieldDef['-multi'] = $fieldDef['_multi'];
			$fieldDef['_children'] = [$fieldDef['_children']];
		}

		if(isset($fieldDef['_cardinality']))
		{
			$this->cardinality = $fieldDef['_cardinality'];
		}

		parent::__construct($fieldDef, $form);

		if(isset($fieldDef['_children']))
		{
			$this->addChildren($fieldDef['_children']);
		}
	}

	public function addChildren($fieldDefs)
	{
		$add = $this->form->processFieldDefs($fieldDefs);

		$this->children = array_merge(
			$add
			, $this->children
		);

		foreach($add as $child)
		{
			$this->subjugate($child);
		}
	}

	public function fields()
	{
		return $this->children;
	}

	public function set($values, $override = false)
	{
		if(!is_array($values))
		{
			$values = [$values];
		}

		$this->values = $values + $this->values;

		$prototype = NULL;

		if($this->multi && isset($this->children[0]))
		{
			$prototype = $this->children[0];

			$this->children = [-1 => NULL] + $this->children;

			$this->children[-1] = clone $prototype;
			$this->children[-1]->disabled = true;
			$this->children[-1]->name = -1;
			$this->children[-1]->suppress = true;

			if($this->children[-1] instanceof Fieldset)
			{
				$this->children[-1]->set([]);	
			}
			else
			{
				$this->children[-1]->set('');
			}
		}

		$childNames = array_flip(array_keys($this->children));

		foreach($values as $fieldName => $fieldValue)
		{
			if($this->multi && $prototype)
			{
				if(!isset($this->children[$fieldName]))
				{
					$this->children[$fieldName] = clone $prototype;
					$this->children[$fieldName]->name = $fieldName;
					$this->children[$fieldName]->set($fieldValue);
				}
			}

			if(isset($this->children[$fieldName]))
			{
				unset($childNames[$fieldName]);

				$this->children[$fieldName]->set($fieldValue);
			}
		}

		$childNames = array_flip($childNames);

		foreach($childNames as $childName)
		{
			if($this->children[$childName] instanceof Fieldset)
			{
				$this->children[$childName]->set([]);	
			}
			else if(!$this->children[$childName]->suppress())
			{
				$this->children[$childName]->set('');
			}
		}
	}

	public function value(Form $form)
	{
		return $form->getValues($this);
	}

	public function render($theme)
	{
		$cardinality = $this->cardinality ? $this->cardinality : 1;

		if($this->multi && $cardinality < count($this->values))
		{
			$cardinality = count($this->values);
		}

		$fields = [];

		$delta = -1;

		foreach($this->children as $name => $field)
		{
			$fields[] = $field->render($theme);
		}

		/*do{
			foreach($this->children as $name => $field)
			{

				$field->disabled = false;

				if($delta < 0 && $this->multi)
				{
					$field->disabled = true;
					continue;
				}
				else if(!$this->multi)
				{
					$delta++;
				}

				$field->setDelta($delta);

				if(isset($this->values[$name]))
				{
					var_dump([$name, $delta], 'MMMMMMMMMMMMMM');

					$field->set($this->values[$name]);
				}
				else if($this->multi && isset($this->values[$delta]))
				{
					//$field->set($this->values[$delta]);	
				}

				$fields[] = $field->render($theme);

				if(!isset($this->values[$name]))
				{
					if($field instanceof Fieldset)
					{
						$field->set([]);	
					}
					else
					{
						$field->set('');
					}
				}
			}

			$delta++;
		} while($delta < $cardinality && $this->multi);
		*/

		$rendered = $theme::render($this, [
			'fields' => $fields
			, 'title' => $this->title
			, 'name' => $this->name
			, 'fullname' => $this->fullname()
			, 'array' => $this->array
			, 'multi' => $this->multi
			, 'delta' => $this->delta
			, 'value' => NULL
			, 'attrs' => $this->attrs()
			, 'disabled' => $this->disabled
			, 'superior' => $this->superior
		]);

		return $rendered;
	}

	public function subjugate($field)
	{
		$field->superior = $this;
	}

	public function isArray()
	{
		return $this->array;
	}

	public function isMulti()
	{
		return $this->multi;
	}

	public function __clone()
	{
		foreach($this->children as $name => &$child)
		{
			$child = clone $this->children[$name];
			$child->superior = $this;
		}
	}
}