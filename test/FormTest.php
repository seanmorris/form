<?php
namespace SeanMorris\Form\Test;
/**
 * Tests the Form class and Field classes and subclasses.
 */
class FormTest extends \SeanMorris\Theme\Test\HtmlTestCase
{
	/**
	 * Tests a simple form.
	 *
	 * ensures a text input is editable and renders correctly.
	 */
	public function testSimpleForm()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'text'
				, '_title' => 'Test Field'
			]
		]);

		$renderedForm = (string)$form->render();

		$tag = $this->getTag($renderedForm, 'input', ['name' => 'testField']);
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		$value = $this->getAttr($tag, 'value');
		$this->assertEqual($value, '', 'Value in HTML set for no reason.');

		$testValue = 'abcde';

		$testValues = ['testField' => $testValue];

		$form->setValues($testValues);

		$formVals = $form->getValues();

		$this->assertEqual(
			$formVals['testField']
			, $testValues['testField']
			, 'Bad value returned for testField.'
		);

		$renderedForm = (string)$form->render();

		$tag = $this->getTag($renderedForm, 'input', ['name' => 'testField']);
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		$value = $this->getAttr($tag, 'value');
		$this->assertEqual($value, $testValue, 'Value in HTML does not match supplied value.');
	}

	/**
	 * Tests a form with a locked field.
	 *
	 * Locked fields will not change their internal values.
	 */
	public function testLockedField()
	{
		$default = 'wxyz';

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'text'
				, '_title' => 'Test Field'
				, '_lock' => TRUE
				, 'value' => $default
			]
		]);

		$testValues = ['testField' => 'abcd'];

		$form->setValues($testValues);

		$formVals = $form->getValues();

		$this->assertEqual(
			$formVals['testField']
			, $default
			, 'Non-default value returned for locked field: testField.'
		);

		$renderedForm = (string)$form->render();

		$this->assertEqual(
			$formVals['testField']
			, $default
			, 'Non-default value rendered for overridden locked field: testField.'
		);

		$form->setValues($testValues, TRUE);

		$formVals = $form->getValues();

		$this->assertEqual(
			$formVals['testField']
			, $testValues['testField']
			, 'Bad value returned for overridden locked field: testField.'
		);

		$renderedForm = (string)$form->render();

		$tag = $this->getTag(
			$renderedForm
			, 'input'
			, ['name' => 'testField']
		);
		
		$this->assertTrue(
			$tag
			, 'Field tag not found in rendered form.'
		);

		$value = $this->getAttr($tag, 'value');

		$this->assertEqual(
			$value
			, $testValues['testField']
			, 'Bad value rendered for overridden locked field: testField.'
		);
	}

	/**
	 * Tests a form with a suppressed field.
	 * 
	 * Suppressed fields can take submitted values
	 * but will NEVER render them (i.e. password fields).
	 */
	public function testSuppressedField()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'text'
				, '_title' => 'Test Field'
				, '_suppress' => TRUE
			]
		]);

		$testValue = 'abcd';
		$testValues = ['testField' => $testValue];

		$form->setValues($testValues);

		$formVals = $form->getValues();

		$this->assertEqual(
			$formVals['testField']
			, $testValue
			, 'Bad value returned for suppressed field: testField.'
		);

		$renderedForm = (string)$form->render();

		$tag = $this->getTag($renderedForm, 'input', ['name' => 'testField']);
		$this->assertTrue($tag, 'Password Field tag not found in rendered form.');

		$value = $this->getAttr($tag, 'value');
		$this->assertEqual($value, '', 'Password fields should never render values in HTML.');
	}

	/**
	 * Returns a basic skeleton for a fieldset.
	 */
	protected function fieldsetSkeleton()
	{
		return [
			'testFieldset' => [
				'type' => 'fieldset'
				, '_title' => 'Test Fields'
				, '_children' => [
					'testA' => [
						'_title' => 'Test A'
						, 'type' => 'text'
						, 'value' => 9
					]
					, 'testB' => [
						'_title' => 'Test B'
						, 'type' => 'text'
					]
					, 'testC' => [
						'_title' => 'Test C'
						, 'type' => 'text'
					]
					,
				]
			]
		];
	}

	/**
	 * Tests a flat fieldset.
	 *
	 * Flat fieldets don't affect their fields beyond grouping them.
	 */
	public function testFlatFieldSet()
	{
		// testD does NOT exist on the form/
		// Its there to test filtering.
		$testValues = [
			'testA' => 2
			, 'testB' => 1
			, 'testC' => 2
			, 'testD' => 3
		];

		$skeleton = $this->fieldsetSkeleton();
		$flatForm = new \SeanMorris\Form\Form($skeleton);

		$renderedForm = (string)$flatForm->render();
		$tag = $this->getTag($renderedForm, 'fieldset');
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		foreach($testValues as $fieldName => $value)
		{
			if($fieldName == 'testD')
			{
				continue;
			}

			$childTag = $this->getTag($tag, 'input', ['name' => $fieldName]);

			$this->assertTrue($childTag, sprintf('Child tag %s not found in rendered fieldset.', $fieldName));
		}

		$flatForm->setValues($testValues);

		$formVals = $flatForm->getValues();

		$this->assertNotEqual(
			$formVals
			, $testValues
			, 'Extra value returned for getValues on flat form.'
		);

		unset($testValues['testD']);

		$this->assertEqual(
			$formVals
			, $testValues
			, 'Bad value returned for getValues on flat form.'
		);

		$renderedForm = (string)$flatForm->render();
		$tag = $this->getTag($renderedForm, 'fieldset');
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		foreach($testValues as $fieldName => $value)
		{
			if($fieldName == 'testD')
			{
				continue;
			}

			$childTag = $this->getTag($tag, 'input', ['name' => $fieldName]);
			$childVal = $this->getAttr($childTag, 'value');

			$this->assertEqual($childVal, $value, sprintf('Child tag %s rendered value incorrect.', $fieldName));
		}
	}

	/**
	 * Tests an array-type fieldset.
	 *
	 * These should become the "parents" of their submitted values.
	 * Such that their fields will be submitted as fieldset[fieldname]=value.
	 */
	public function testArrayFieldSet()
	{
		$skeleton = $this->fieldsetSkeleton();

		$testValues = ['testFieldset' => [
			'testA' => 2
			, 'testB' => 1
			, 'testC' => 2
			, 'testD' => 3
		]];

		$skeleton['testFieldset'] += ['_array' => TRUE];
		$arrayForm = new \SeanMorris\Form\Form($skeleton);

		$renderedForm = (string)$arrayForm->render();
		$tag = $this->getTag($renderedForm, 'fieldset');
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		foreach($testValues['testFieldset'] as $fieldName => $value)
		{
			if($fieldName == 'testD')
			{
				continue;
			}

			$childName = sprintf('testFieldset[%s]', $fieldName);
			$childTag = $this->getTag($tag, 'input', ['name' => $childName]);

			$this->assertTrue($childTag, sprintf('Child tag %s not found in rendered fieldset.', $childName));
		}

		$arrayForm->setValues($testValues);

		$formVals = $arrayForm->getValues();

		$this->assertNotEqual(
			$formVals
			, $testValues
			, 'Bad value returned for getValues on array form.'
		);

		unset($testValues['testFieldset']['testD']);

		$this->assertEqual(
			$formVals
			, $testValues
			, 'Bad value returned for getValues on array form.'
		);

		$renderedForm = (string)$arrayForm->render();

		$tag = $this->getTag($renderedForm, 'fieldset');
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		foreach($testValues['testFieldset'] as $fieldName => $value)
		{
			if($fieldName == 'testD')
			{
				continue;
			}

			$childName = sprintf('testFieldset[%s]', $fieldName);
			$childTag = $this->getTag($tag, 'input', ['name' => $childName]);
			$childVal = $this->getAttr($childTag, 'value');

			$this->assertEqual($childVal, $value, sprintf('Child tag %s rendered value incorrect.', $fieldName));
		}
	}

	/**
	 * Tests a multi-type fieldset.
	 *
	 * These allow multiple sets of grouped values to be submitted.
	 *
	 * Like array-type fieldsets, these should become the
	 * "parents" of their submitted values, but with an added
	 * integer key, i.e. fieldset[1][fieldname] 
	 */
	public function testMultiFieldSet()
	{
		$skeleton = $this->fieldsetSkeleton();
		$skeleton['testFieldset'] += ['_array' => TRUE];
		$skeleton['testFieldset'] += ['_multi' => TRUE];
		$skeleton['testFieldset']['_children'] = [
			'_title' => 'test'
			, 'type' => 'fieldset'
			, '_array' => TRUE
			, '_children' => [
				'testA' => [
					'_title' => 'Test A'
					, 'type' => 'text'
					, 'value' => 9
				]
				, 'testB' => [
					'_title' => 'Test B'
					, 'type' => 'text'
				]
				, 'testC' => [
					'_title' => 'Test C'
					, 'type' => 'text'
				]
				,
			]
		];

		$multiValues = [ 'testFieldset' => [
			0 => [
				'testA' => 111
				, 'testB' => 222
				, 'testC' => 333
			]
			, 1 => [
				'testA' => 999
				, 'testB' => 888
				, 'testC' => 777
				, 'testD' => 666
			]
		]];

		$multiForm = new \SeanMorris\Form\Form($skeleton);

		$renderedForm = (string)$multiForm->render();
		$tag = $this->getTag($renderedForm, 'fieldset');
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		foreach($multiValues['testFieldset'] as $fieldset => $fields)
		{
			if($fieldset == 1)
			{
				continue;
			}

			foreach($fields as $fieldName => $value)
			{
				if($fieldName == 'testD')
				{
					continue;
				}

				$childName = sprintf('testFieldset[%d][%s]', $fieldset, $fieldName);
				$childTag = $this->getTag($tag, 'input', ['name' => $childName]);
				$this->assertTrue($childTag, sprintf('Child tag %s not found in rendered fieldset.', $childName));
			}
		}

		$multiForm->setValues($multiValues);

		$formVals = $multiForm->getValues();

		$this->assertNotEqual(
			$formVals
			, $multiValues
			, 'Bad value returned for getValues on multi form.'
		);

		unset($multiValues['testFieldset'][1]['testD']);

		$this->assertEqual(
			$formVals
			, $multiValues
			, 'Bad value returned for getValues on multi form.'
		);

		$renderedForm = (string)$multiForm->render();
		$tag = $this->getTag($renderedForm, 'fieldset');
		$this->assertTrue($tag, 'Field tag not found in rendered form.');

		foreach($multiValues['testFieldset'] as $fieldset => $fields)
		{
			foreach($fields as $fieldName => $value)
			{
				if($fieldName == 'testD')
				{
					continue;
				}

				$childName = sprintf('testFieldset[%d][%s]', $fieldset, $fieldName);
				$childTag = $this->getTag($tag, 'input', ['name' => $childName]);
				$this->assertTrue($childTag, sprintf('Child tag %s not found in rendered fieldset.', $childName));
			}
		}
	}

	/**
	 * Tests a Text field.
	 */
	public function testTextFieldHtml()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'text'
				, '_title' => 'Test Field'
			]
		]);

		$testValue = 'This is a testing value. ' . mt_rand();

		$form->setValues(['testField' => $testValue]);
		$values = $form->getValues();

		$renderedForm = (string)$form->render();

		$tag = $this->getTag($renderedForm, 'input', ['name' => 'testField']);
		$this->assertTrue($tag, 'Text Field tag not found in rendered form.');

		$value = $this->getAttr($tag, 'value');
		$this->assertEqual($value, $testValue, 'Text field value not found in rendered HTML.');

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'text'
				, '_title' => 'Test Field'
				, '_required' => 'Test field is required.'
			]
		]);

		$renderedForm = (string)$form->render();
		$tag = $this->getTag($renderedForm, 'span', ['class' => 'required']);
		$this->assertTrue($tag, 'Required annotation not found on text field.');		
	}

	/**
	 * Tests a password field.
	 */
	public function testPasswordFieldHtml()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'password'
				, '_title' => 'Test Field'
			]
		]);

		$testValue = 'This is a testing value. ' . mt_rand();

		$form->setValues(['testField' => $testValue]);
		$values = $form->getValues();

		$renderedForm = (string)$form->render();

		$tag = $this->getTag($renderedForm, 'input', ['name' => 'testField']);
		$this->assertTrue($tag, 'Password Field tag not found in rendered form.');

		$value = $this->getAttr($tag, 'value');
		$this->assertEqual($value, '', 'Password fields should never render values in HTML.');

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'password'
				, '_title' => 'Test Field'
				, '_required' => 'Test field is required.'
			]
		]);

		$renderedForm = (string)$form->render();
		$tag = $this->getTag($renderedForm, 'span', ['class' => 'required']);
		$this->assertTrue($tag, 'Required annotation not found on password field.');
	}

	/**
	 * Tests a select field.
	 */
	public function testSelectFieldHtml()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'select'
				, '_title' => 'Test Field'
				, '_options' => [
					'a'
					, 'b'
					, 'c'
					, 'd'
				]
			]
		]);

		$testValue = rand(0,3);

		$form->setValues(['testField' => $testValue]);
		$renderedForm = (string)$form->render();

		$tag = $this->getTag($renderedForm, 'select');
		$this->assertTrue($tag, 'Select tag not found in rendered form.');
		
		$tag = $this->getTag($renderedForm, 'option', ['value' => $testValue]);
		$this->assertTrue($tag, 'Option tag not found in rendered form.');

		$value = $this->getAttr($tag, 'selected');
		$this->assertEqual($value, 'selected', 'Option is not selected in HTML.');

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'select'
				, '_title' => 'Test Field'
				, '_options' => [
					'a'
					, 'b'
					, 'c'
					, 'd'
				]
				, '_required' => 'Test field is required.'
			]
		]);

		$renderedForm = (string)$form->render();
		$tag = $this->getTag($renderedForm, 'span', ['class' => 'required']);
		$this->assertTrue($tag, 'Required annotation not found on select field.');
	}

	/**
	 * Tests a set of radio buttons.
	 */
	public function testRadioFieldHtml()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'radios'
				, '_title' => 'Test Field'
				, '_options' => [
					'a'
					, 'b'
					, 'c'
					, 'd'
				]
			]
		]);

		$testValue = rand(0,3);

		$form->setValues(['testField' => $testValue]);
		$renderedForm = (string)$form->render();

		$tag = $this->getTag($renderedForm, 'input', ['value' => $testValue]);
		$this->assertTrue($tag, 'Radiobutton field tag not found in rendered form.');

		$value = $this->getAttr($tag, 'checked');
		$this->assertEqual($value, 'checked', 'Option is not selected in HTML.');

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'radio'
				, '_title' => 'Test Field'
				, '_options' => [
					'a'
					, 'b'
					, 'c'
					, 'd'
				]
				, '_required' => 'Test field is required.'
			]
		]);

		$renderedForm = (string)$form->render();
		$tag = $this->getTag($renderedForm, 'span', ['class' => 'required']);
		$this->assertTrue($tag, 'Required annotation not found on radio field.');
	}

	/**
	 * Tests a checkbox field.
	 */
	public function testCheckBoxFieldHtml()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'checkbox'
				, '_title' => 'Test Field'
			]
		]);

		$renderedForm = (string)$form->render();
		
		$tag = $this->getTag($renderedForm, 'input');
		$this->assertTrue($tag, 'Checkbox field tag not found in rendered form.');
		
		$value = $this->getAttr($tag, 'checked');
		$this->assertFalse($value, 'Checkbox checked for no reason.');

		$form->setValues(['testField' => TRUE]);
		$renderedForm = (string)$form->render();
		
		$tag = $this->getTag($renderedForm, 'input');
		$this->assertTrue($tag, 'Checkbox field tag not found in rendered form.');
		
		$value = $this->getAttr($tag, 'checked');
		$this->assertTrue($value, 'Checkbox not checked.');

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'checkbox'
				, '_title' => 'Test Field'
				, '_required' => 'Test field is required.'
			]
		]);

		$renderedForm = (string)$form->render();
		$tag = $this->getTag($renderedForm, 'span', ['class' => 'required']);
		$this->assertTrue($tag, 'Required annotation not found on checkbox field.');
	}

	/**
	 * Tests a button field.
	 */
	public function testButtonFieldHtml()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'button'
				, '_title' => 'Test Field'
			]
		]);

		$renderedForm = (string)$form->render();
		
		$tag = $this->getTag($renderedForm, 'input', ['type' => 'button']);
		$this->assertTrue($tag, 'Button tag not found in rendered form.');
	}

	/**
	 * Tests a submit field.
	 */
	public function testSubmitFieldHtml()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
			]
		]);

		$renderedForm = (string)$form->render();
		
		$tag = $this->getTag($renderedForm, 'input', ['type' => 'submit']);
		$this->assertTrue($tag, 'Button tag not found in rendered form.');
	}

	/**
	 * Tests the Required validator.
	 */
	public function testRequiredValidator()
	{
		$testErrorMessage = "Test field is required.";

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_validators' => [
					'SeanMorris\Form\Validator\Required' => $testErrorMessage
				]
			]
		]);

		$form->validate([]);

		$this->assertTrue(
			in_array($testErrorMessage, $form->errors())
			, 'Required error not found.'
		);

		$form->validate(['testField' => 'Test Value']);

		$this->assertFalse(
			in_array($testErrorMessage, $form->errors())
			, 'Required error found with value present.'
		);

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_required' => $testErrorMessage
			]
		]);

		$form->validate([]);

		$this->assertTrue(
			in_array($testErrorMessage, $form->errors())
			, 'Required error not found. Shorthand syntax.'
		);

		$form->validate(['testField' => 'Test Value']);

		$this->assertFalse(
			in_array($testErrorMessage, $form->errors())
			, 'Required error found with value present. Shorthand syntax.'
		);
	}

	/**
	 * Tests the Range validator.
	 */
	public function testRangeValidator()
	{
		$testMinMessage = "Test field must be at least 5.";
		$testMaxMessage = "Test field must be at most 10.";
		$testNanMessage = "Test field must be a number.";

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_validators' => [
					'SeanMorris\Form\Validator\Range' => [
						10  => $testMaxMessage
						, 5 => $testMinMessage
						, 'nan' => $testNanMessage
					]
				]
			]
		]);

		$form->validate([]);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason.');

		$form->validate(['testField' => 2]);
		
		$this->assertTrue(
			in_array($testMinMessage, $form->errors())
			, 'Minumum value error not found.'
		);

		$form->validate(['testField' => 12]);
		
		$this->assertTrue(
			in_array($testMaxMessage, $form->errors())
			, 'Maxiumum value error not found.'
		);

		$form->validate(['testField' => 'STRING DATA']);
		
		$this->assertTrue(
			in_array($testNanMessage, $form->errors())
			, 'Nan value error not found.'
		);

		$form->validate(['testField' => 7]);
		
		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason.');

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_range' => [
					10  => $testMaxMessage
					, 5 => $testMinMessage
					, 'nan' => $testNanMessage
				]
			]
		]);

		$form->validate([]);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason. Shorthand syntax.');

		$form->validate(['testField' => 2]);
		
		$this->assertTrue(
			in_array($testMinMessage, $form->errors())
			, 'Minumum value error not found. Shorthand syntax.'
		);

		$form->validate(['testField' => 12]);
		
		$this->assertTrue(
			in_array($testMaxMessage, $form->errors())
			, 'Maxiumum value error not found. Shorthand syntax.'
		);

		$form->validate(['testField' => 'STRING DATA']);
		
		$this->assertTrue(
			in_array($testNanMessage, $form->errors())
			, 'Nan value error not found. Shorthand syntax.'
		);

		$form->validate(['testField' => 7]);
		
		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason. Shorthand syntax.');
	}

	/**
	 * Tests the Regex validator.
	 */
	public function testRegexValidator()
	{
		$testErrorMessage1 = "Test field must start with a letter.";
		$testErrorMessage2 = "Test field must be 2 words.";

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_validators' => [
					'SeanMorris\Form\Validator\Regex' => [
						'/^[A-Za-z]/' => $testErrorMessage1
						, '/^\w+\s\w+?$/' => $testErrorMessage2
					]
				]
			]
		]);

		$form->validate([]);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason.');

		$form->validate(['testField' => 'Test Value']);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason.');

		$form->validate(['testField' => '123Test Value']);

		$this->assertTrue(
			in_array($testErrorMessage1, $form->errors())
			, 'Pattern not letter-first matching regex should throw letter-first error.'
		);

		$form->validate(['testField' => 'TestValue']);

		$this->assertTrue(
			in_array($testErrorMessage2, $form->errors())
			, 'Pattern not matching two-word regex should throw two-word error.'
		);

		$form->validate(['testField' => '123TestValue']);

		$this->assertTrue(
			in_array($testErrorMessage1, $form->errors())
			, 'Pattern not letter-first matching regex should throw letter-first error.'
		);

		$this->assertTrue(
			in_array($testErrorMessage2, $form->errors())
			, 'Pattern not matching two-word regex should throw two-word error.'
		);

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_regex' => [
					'/^[A-Za-z]/' => $testErrorMessage1
					, '/^\w+\s\w+?$/' => $testErrorMessage2
				]
			]
		]);

		$form->validate([]);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason. Shorthand syntax.');

		$form->validate(['testField' => 'Test Value']);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason. Shorthand syntax.');

		$form->validate(['testField' => '123Test Value']);

		$this->assertTrue(
			in_array($testErrorMessage1, $form->errors())
			, 'Pattern not letter-first matching regex should throw letter-first error. Shorthand syntax.'
		);

		$form->validate(['testField' => 'TestValue']);

		$this->assertTrue(
			in_array($testErrorMessage2, $form->errors())
			, 'Pattern not matching two-word regex should throw two-word error. Shorthand syntax.'
		);

		$form->validate(['testField' => '123TestValue']);

		$this->assertTrue(
			in_array($testErrorMessage1, $form->errors())
			, 'Pattern not letter-first matching regex should throw letter-first error. Shorthand syntax.'
		);

		$this->assertTrue(
			in_array($testErrorMessage2, $form->errors())
			, 'Pattern not matching two-word regex should throw two-word error. Shorthand syntax.'
		);
	}

	/**
	 * Tests the Required validator.
	 */
	public function testEmailValidator()
	{
		$testErrorMessage = "Test field must be a valid email.";

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_validators' => [
					'SeanMorris\Form\Validator\Email' => $testErrorMessage
				]
			]
		]);

		$form->validate([]);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason.');

		$form->validate(['testField' => 'Test Value']);

		$this->assertTrue(
			in_array($testErrorMessage, $form->errors())
			, 'Invalid email error not found.'
		);

		$form->validate(['testField' => 'person@example.com']);

		$this->assertFalse(
			in_array($testErrorMessage, $form->errors())
			, 'Invalid email error found with valid email present.'
		);

		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'submit'
				, '_title' => 'Test Field'
				, '_email' => $testErrorMessage
			]
		]);

		$form->validate([]);

		$this->assertTrue(empty($form->errors()), 'Error thrown for no reason. Shorthand syntax.');

		$form->validate(['testField' => 'Test Value']);

		$this->assertTrue(
			in_array($testErrorMessage, $form->errors())
			, 'Invalid email error not found. Shorthand syntax.'
		);

		$form->validate(['testField' => 'person@example.com']);

		$this->assertFalse(
			in_array($testErrorMessage, $form->errors())
			, 'Invalid email error found with valid email present. Shorthand syntax.'
		);
	}
}