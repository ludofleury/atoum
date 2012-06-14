<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\duration;

use
	mageekguy\atoum\test,
	mageekguy\atoum\runner,
	mageekguy\atoum\locale,
	mageekguy\atoum\dependencies,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\duration
;

require_once __DIR__ . '/../../../../../runner.php';

class phing extends test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\duration\cli') ;
	}

	public function test__construct()
	{
		$this
			->if($field = new duration\phing())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getDurationColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(runner::runStop))
			->if($dependencies = new dependencies())
			->and($dependencies[$this->getTestedClassName()]['locale'] = $locale = new locale())
			->and($field = new duration\phing($prompt = new prompt(), $titleColorizer = new colorizer(), $durationColorizer = new colorizer(), $dependencies))
			->then
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getDurationColorizer())->isIdenticalTo($durationColorizer)
				->object($field->getLocale())->isIdenticalTo($locale)
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(runner::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new duration\phing())
			->then
				->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
			->if($field = new duration\phing(new prompt(uniqid())))
			->then
				->object($field->setPrompt($otherPrompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($otherPrompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this
			->if($field = new duration\phing())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new duration\phing(null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetDurationColorizer()
	{
		$this
			->if($field = new duration\phing())
			->then
				->object($field->setDurationColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getDurationColorizer())->isIdenticalTo($colorizer)
			->if($field = new duration\phing(null, null, new colorizer()))
			->then
				->object($field->setDurationColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getDurationColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new duration\phing())
			->then
				->boolean($field->handleEvent(runner::runStart, new runner()))->isFalse()
				->variable($field->getValue())->isNull()
			->if($runner = new \mock\mageekguy\atoum\runner())
			->and($runner->getMockController()->getRunningDuration = $runningDuration = rand(0, PHP_INT_MAX))
			->then
				->boolean($field->handleEvent(runner::runStop, $runner))->isTrue()
				->integer($field->getValue())->isEqualTo($runningDuration)
		;
	}

	public function test__toString()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\runner())
			->and($runner->getMockController()->getRunningDuration = 1)
			->and($prompt = new \mock\mageekguy\atoum\cli\prompt())
			->and($prompt->getMockController()->__toString = $promptString = uniqid())
			->and($titleColorizer = new \mock\mageekguy\atoum\cli\colorizer())
			->and($titleColorizer->getMockController()->colorize = $colorizedTitle = uniqid())
			->and($durationColorizer = new \mock\mageekguy\atoum\cli\colorizer())
			->and($durationColorizer->getMockController()->colorize = $colorizedDuration = uniqid())
			->and($locale = new \mock\mageekguy\atoum\locale())
			->and($locale->getMockController()->_ = function($string) { return $string; })
			->and($dependencies = new dependencies())
			->and($dependencies[$this->getTestedClassName()]['locale'] = $locale)
			->and($field = new duration\phing($prompt, $titleColorizer, $durationColorizer, $dependencies))
			->then
				->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
				->mock($locale)
					->call('_')->withArguments('Running duration')->once()
					->call('_')->withArguments('unknown')->once()
					->call('_')->withArguments('%1$s: %2$s.')->once()
				->mock($titleColorizer)
					->call('colorize')->withArguments('Running duration')->once()
				->mock($durationColorizer)
					->call('colorize')->withArguments('unknown')->once()
			->if($titleColorizer->getMockController()->resetCalls())
			->and($durationColorizer->getMockController()->resetCalls())
			->and($locale->getMockController()->resetCalls())
			->and($field->handleEvent(runner::runStart, $runner))
			->then
				->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
				->mock($locale)
					->call('_')->withArguments('Running duration')->once()
					->call('_')->withArguments('unknown')->once()
					->call('_')->withArguments('%1$s: %2$s.')->once()
				->mock($titleColorizer)
					->call('colorize')->withArguments('Running duration')->once()
				->mock($durationColorizer)
					->call('colorize')->withArguments('unknown')->once()
			->if($titleColorizer->getMockController()->resetCalls())
			->and($durationColorizer->getMockController()->resetCalls())
			->and($locale->getMockController()->resetCalls())
			->and($field->handleEvent(runner::runStop, $runner))
			->then
				->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
				->mock($locale)
					->call('_')->withArguments('Running duration')->once()
					->call('__')->withArguments('%4.2f second', '%4.2f seconds', 1)->once()
					->call('_')->withArguments('%1$s: %2$s.')->once()
				->mock($titleColorizer)
					->call('colorize')->withArguments('Running duration')->once()
				->mock($durationColorizer)
					->call('colorize')->withArguments('1.00 second')->once()
			->if($titleColorizer->getMockController()->resetCalls())
			->and($durationColorizer->getMockController()->resetCalls())
			->and($locale->getMockController()->resetCalls())
			->and($runner->getMockController()->getRunningDuration = $runningDuration = rand(2, PHP_INT_MAX))
			->and($field = new duration\phing($prompt, $titleColorizer, $durationColorizer, $dependencies))
			->and($field->handleEvent(runner::runStart, $runner))
			->then
				->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
				->mock($locale)
					->call('_')->withArguments('Running duration')->once()
					->call('_')->withArguments('unknown')->once()
					->call('_')->withArguments('%1$s: %2$s.')->once()
				->mock($titleColorizer)
					->call('colorize')->withArguments('Running duration')->once()
				->mock($durationColorizer)
					->call('colorize')->withArguments('unknown')->once()
			->if($titleColorizer->getMockController()->resetCalls())
			->and($durationColorizer->getMockController()->resetCalls())
			->and($locale->getMockController()->resetCalls())
			->and($field->handleEvent(runner::runStop, $runner))
			->then
				->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
				->mock($locale)
					->call('_')->withArguments('Running duration')->once()
					->call('__')->withArguments('%4.2f second', '%4.2f seconds', $runningDuration)->once()
					->call('_')->withArguments('%1$s: %2$s.')->once()
				->mock($titleColorizer)
					->call('colorize')->withArguments('Running duration')->once()
				->mock($durationColorizer)
					->call('colorize')->withArguments(sprintf('%4.2f', $runningDuration) . ' seconds')->once()
		;
	}
}

?>
