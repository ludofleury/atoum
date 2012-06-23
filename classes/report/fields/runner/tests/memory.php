<?php

namespace atoum\report\fields\runner\tests;

use
	atoum,
	atoum\report,
	atoum\runner
;

abstract class memory extends report\field
{
	protected $value = null;
	protected $testNumber = null;

	public function __construct(atoum\locale $locale = null)
	{
		parent::__construct(array(runner::runStop), $locale);
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getTestNumber()
	{
		return $this->testNumber;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->value = $observable->getScore()->getTotalMemoryUsage();
			$this->testNumber = $observable->getTestNumber();

			return true;
		}
	}
}
