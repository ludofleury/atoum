<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpClass
;

class phpClass extends php\tokenizer
{
	protected $name = null;
	protected $parent = null;

	private $stack = null;
	private $nextTokenIsName = false;
	private $nextTokenIsParent = false;

	public function __construct($string = null)
	{
		$this->putInString($this->name)->valueOfToken(T_STRING)->afterToken(T_CLASS)->skipToken(T_WHITESPACE);
		$this->putInString($this->parent)->valueOfToken(T_STRING)->afterToken(T_EXTENDS)->skipToken(T_WHITESPACE);
		$this->putInArray($this->interfaces)->valueOfToken(T_STRING)->afterToken(T_IMPLEMENTS)->skipToken(T_WHITESPACE)->skipValue(',');

		parent::__construct($string);
	}

	public function canTokenize(tokenizer\tokens $tokens)
	{
		$canTokenize = $tokens->currentTokenHasName(T_CLASS);

		if (
				$canTokenize === false
				&&
				(
					$tokens->currentTokenHasName(T_FINAL)
					||
					$tokens->currentTokenHasName(T_ABSTRACT)
				)
				&&
				$tokens->valid() === true
			)
		{
			$key = $tokens->key();

			$goToNextToken = true;

			while ($tokens->valid() === true && $goToNextToken === true)
			{
				$tokens->next();

				switch (true)
				{
					case $tokens->currentTokenHasName(T_WHITESPACE):
					case $tokens->currentTokenHasName(T_FINAL):
					case $tokens->currentTokenHasName(T_ABSTRACT):
						break;

					case  $tokens->currentTokenHasName(T_CLASS):
						$canTokenize = true;
						$goToNextToken = false;
						break;

					default:
						$goToNextToken = false;
				}
			}

			$tokens->seek($key);
		}

		return $canTokenize;
	}

	public function tokenize($string)
	{
		$tokens = new tokenizer\tokens($string);

		if ($tokens->valid() === true && $tokens->current()->getName() === T_OPEN_TAG)
		{
			$tokens->next();
		}

		return $this->setFromTokens($tokens);
	}

	public function getName()
	{
		return ($this->name ?: null);
	}

	public function getParent()
	{
		return ($this->parent ?: null);
	}

	public function getFunctions()
	{
		return $this->getIterators(phpClass\phpFunction\iterator::type);
	}

	protected function appendCurrentToken(tokenizer\tokens $tokens)
	{
		parent::appendCurrentToken($tokens);

		switch ($tokens->current()->getValue())
		{
			case '{':
				++$this->stack;
				break;

			case '}':
				--$this->stack;
				break;
		}

		if ($this->stack === 0)
		{
			$this->stack = null;
			$this->stop();
		}

		return $this;
	}

	public function getIteratorInstance()
	{
		return new phpClass\iterator();
	}
}

?>
