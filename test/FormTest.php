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

			$this->assertEqual($childVal, $value, sprintf('Child tag %s value incorrect.', $fieldName));
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

		$multiForm = new \SeanMorris\Form\Form($skeleton);
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
		$this->assertTrue($value, 'Checkbox left checked.');
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
}