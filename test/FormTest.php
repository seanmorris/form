<?php
namespace SeanMorris\Form\Test;
class FormTest extends \UnitTestCase
{
	public function testSimpleForm()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'text'
				, '_title' => 'Test Field'
			]
		]);

		$testValue = ['testField' => 'abcd'];

		$form->setValues($testValue);

		$formVals = $form->getValues();

		$this->assertEqual(
			$formVals['testField']
			, $testValue['testField']
			, 'Bad value returned for testField.'
		);
	}

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

		$testValue = ['testField' => 'abcd'];

		$form->setValues($testValue);

		$formVals = $form->getValues();

		$this->assertEqual(
			$formVals['testField']
			, $default
			, 'Non-default value returned for locked field: testField.'
		);

		$form->setValues($testValue, TRUE);

		$formVals = $form->getValues();

		$this->assertEqual(
			$formVals['testField']
			, $testValue['testField']
			, 'Bad value returned for overridden locked field: testField.'
		);
	}

	public function testSuppressedField()
	{
		$form = new \SeanMorris\Form\Form([
			'testField' => [
				'type' => 'text'
				, '_title' => 'Test Field'
				, '_suppress' => TRUE
			]
		]);

		$testValue = ['testField' => 'abcd'];

		$form->setValues($testValue);

		$formVals = $form->getValues();

		$this->assertFalse(
			isset($formVals['testField'])
			, 'Non-null value returned for suppressed field: testField.'
		);
	}

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

	public function testFlatFieldSet()
	{
		$testValues = [
			'testA' => 2
			, 'testB' => 1
			, 'testC' => 2
			, 'testD' => 3
		];

		$skeleton = $this->fieldsetSkeleton();
		$flatForm = new \SeanMorris\Form\Form($skeleton);
		$flatForm->setValues($testValues);

		$formVals = $flatForm->getValues();

		$this->assertNotEqual(
			$formVals
			, $testValues
			, 'Bad value returned for getValues on flat form.'
		);

		unset($testValues['testD']);

		$this->assertEqual(
			$formVals
			, $testValues
			, 'Bad value returned for getValues on flat form.'
		);
	}

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
}