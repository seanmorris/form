<?php
namespace SeanMorris\Form;
/**
 * Logic for Fieldsets.
 */
class Fieldset extends Field
{
	//protected $delta;
	/**
	 * List of values passed to child fields.
	 */
	protected $values = [];
	/**
	 * List of child fields.
	 */
	protected $children = [];

	/**
	 * Sets up the fieldset based on the $fieldDef
	 *
	 * @param array $fieldDef Array describing fieldset details.
	 * @param object $form Form that owns this fieldset.
	 */
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

			$this->multi = $fieldDef['-multi'];
		}

		parent::__construct($fieldDef, $form);

		if(isset($fieldDef['_children']))
		{
			$this->addChildren($fieldDef['_children']);
		}

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

			unset($this->children[0]);
		}
	}

	/**
	 * Adds a child to the fieldset.
	 *
	 * @param array $fieldDef fieldDef of field being added.
	 */
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

	/**
	 * Returns a list of the fieldset's children.
	 *
	 * @return array child fields.
	 */
	public function fields()
	{
		return $this->children;
	}

	/**
	 * Sets the fieldset's children.
	 *
	 * @param array list of values.
	 */
	public function set($values, $override = false)
	{

		if(!is_array($values) && !$this->multi && isset($this->children[0]))
		{
			$values = [$values];
		}

		if(!is_array($values))
		{
			return;
		}


		if($this->multi)
		{
			unset($values[-1]);

			ksort($values);

			$nonNumeric = array_filter(array_keys($values), function($x){
				return !is_numeric($x);
			});

			if(!$nonNumeric)
			{
				$values = array_values($values);
			}
		}

		$childNames = array_flip(array_keys($this->children));

		$this->values = $values + $this->values;


		$prototype = NULL;

		if($this->multi && isset($this->children[-1]))
		{
			$prototype = $this->children[-1];
		}

		foreach($values as $fieldName => $fieldValue)
		{
			if($this->multi && $prototype)
			{
				if(!isset($this->children[$fieldName]))
				{
					$this->children[$fieldName] = clone $prototype;
					$this->children[$fieldName]->name = $fieldName;
					$this->children[$fieldName]->set($fieldValue, $override);
				}
			}

			if(isset($this->children[$fieldName]))
			{
				unset($childNames[$fieldName]);

				$this->children[$fieldName]->set($fieldValue, $override);
			}
		}

		$childNames = array_flip($childNames);

		foreach($childNames as $childName)
		{
			if($childName == -1)
			{
				continue;
			}

			\SeanMorris\Ids\Log::error($childName, $values ?? NULL);

			if($this->children[$childName] instanceof Fieldset)
			{
				$this->children[$childName]->clear();
			}
			else if(!$this->children[$childName]->suppress())
			{
				$this->children[$childName]->clear();
			}
		}
	}

	/**
	 * Gets the fieldset's children.
	 *
	 * @return array list of values.
	 */
	public function value()
	{
		$fields = $this->fields();
		$values = [];

		foreach($fields as $fieldName => $field)
		{
			$fieldValue = $field->value();

			if($this->isMulti() && $fieldName == -1)
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

	/**
	 * Renders the fieldet to a view.
	 *
	 * @return object View object for fieldset.
	 */
	public function render($theme)
	{
		$fields = [];

		$delta = -1;

		foreach($this->children as $name => $field)
		{
			$fields[] = $field->render($theme);
		}

		$rendered = $theme::render($this, [
			'fields' => $fields
			, 'title' => $this->title
			, 'name' => $this->name
			, 'fullname' => $this->fullname()
			, 'array' => $this->array
			, 'multi' => $this->multi
			, 'value' => NULL
			, 'attrs' => $this->attrs()
			, 'disabled' => $this->disabled
			, 'superior' => $this->superior
		]);

		return $rendered;
	}

	/**
	 * Informs a child field that this fieldset is its direct owner.
	 *
	 * @param object Field to subjugate.
	 */
	public function subjugate($field)
	{
		$field->superior = $this;
	}

	/**
	 * Returns a boolean indicating whether or not this field can hold an array.
	 *
	 * @return boolean true if field holds an array.
	 */
	public function isArray()
	{
		return $this->array;
	}

	/**
	 * Returns a boolean indicating whether or not this field is multivalued.
	 *
	 * @return boolean true if field is multivalued.
	 */
	public function isMulti()
	{
		return $this->multi;
	}

	/**
	 * Clones this fieldset and children.
	 */
	public function __clone()
	{
		foreach($this->children as $name => &$child)
		{
			$child = clone $this->children[$name];
			$child->superior = $this;
		}
	}

	/**
	 * Validates the fieldset and its fields.
	 *
	 * @return True if no errors were generated.
	 */
	public function validate()
	{
		$this->errors = [];

		parent::validate();

		foreach($this->children as $fieldName => $field)
		{
			if(!$field->validate())
			{
				$this->errors = array_merge($this->errors, $field->errors());
			}
		}

		return !$this->errors;
	}

	public function clear()
	{
		$this->values = [];

		if(!$this->multi)
		{
			return;
		}

		$this->children = [];
		// if(isset($this->children[-1]))
		// {
		// 	$this->children = [-1 => $this->children[-1]];
		// }
		// else
		// {
		// }
	}
}
