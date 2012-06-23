<?php

namespace atoum\tests\units\mock\stream;

use
	atoum\test,
	atoum\mock\stream
;

require_once __DIR__ . '/../../../runner.php';

class invoker extends test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('atoum\test\adapter\invoker');
	}

	public function test__construct()
	{
		$this
			->if($invoker = new stream\invoker($methodName = uniqid()))
			->then
				->string($invoker->getMethodName())->isEqualTo($methodName)
		;
	}
}
