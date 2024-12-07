<?php

namespace App\Classes;

use Random\RandomException;

class RandomAvatar
{
	/** @var array|string[] All possible DiceBear styles available */
	private readonly array $allStyles;

	/** @var string|int Version of the api */
	private string|int $apiVersion = "9.x";

	/** @var string The base URL for the service of the generator. */
	private string $baseUrl = "https://api.dicebear.com";

	private array $returnTypes = [
		"svg", "png", "jpg", "webp", "avif"
	];

	/** @var string Seed for the avatar */
	private string $seed;

	/** @var ?string Style chosen for the avatar */
	private ?string $chosenStyle;

	/** @var string|null Return type of the avatar */
	private ?string $chosenReturnType;

	/**
	 * @throws RandomException
	 */
	public function __construct(
		string $seed,
		?string $style = null,
		?string $returnType = null,
	)
	{
		$this
			->putReadonlyVars()
			->checkRequirements($style, $returnType);

		$this->seed = $seed;
		$this->chosenStyle = $style ?? $this->randomStyle();
		$this->chosenReturnType = $returnType;
	}

	private function putReadonlyVars(): RandomAvatar
	{
		$this->allStyles = [
			"adventurer", "adventurer-neutral", "avataaars", "avataaars-neutral", "big-ears", "big-ears-neutral",
			"big-smile", "bottts", "bottts-neutral", "croodles", "croodles-neutral", "dylan", "fun-emoji", "glass",
			"icons", "identicon", "initials", "lorelei", "lorelei-neutral", "micah", "miniavs", "notionists",
			"notionists", "notionists-neutral", "open-peeps", "personas", "pixel-art", "pixel-art-neutral", "rings",
			"shapes", "thumbs"
		];

		return $this;
	}

	/**
	 * Checks if all the params set in the constructor are valid.
	 * @param string|null $style
	 * @param string|null $returnType
	 * @return RandomAvatar
	 */
	private function checkRequirements(?string $style = null, ?string $returnType = null): RandomAvatar
	{
		if(!is_null($style) && !array_key_exists($style, $this->allStyles)) {
			throw new \RuntimeException("The style '" . $style . "' does not exist or is not supported.");
		}

		if(!is_null($returnType) && !in_array($returnType, $this->returnTypes)) {
			throw new \RuntimeException("The return type '" . $returnType . "' does not exist or is not supported.");
		}

		return $this;
	}

	/**
	 * Builds and returns the URL for the random avatar
	 * @return string
	 */
	public function buildUrl(): string
	{
		$finalUrl = $this->baseUrl;

		$finalUrl = $this->addCharacterIfNeeded($finalUrl);

		//add url path bits
		$urlPathBits = [
			(string) $this->apiVersion,
			(string) $this->chosenStyle,
			(string) $this->chosenReturnType
		];
		$finalUrl .= implode("/", $urlPathBits);

		//add get bits
		$getParams = [
			"seed" => $this->seed,
			"size" => 1000
		];

		$finalUrl .= "?";

		foreach ($getParams as $paramKey => $paramValue) {
			$finalUrl .= $this->addCharacterIfNeeded($finalUrl, "&")
				. $paramKey . "=" . $paramValue;
		}

		return $finalUrl;
	}

	/**
	 * Adds a character if the string doesn't end by specified character
	 * @param string $string
	 * @param string $character
	 * @return string
	 */
	private function addCharacterIfNeeded(string $string, string $character = "/"): string
	{
		$stringEnd = str_ends_with($string, $character)
			? ""
			: $character;

		return $string . $stringEnd;
	}

	/**
	 * Returns a random style to be applied.
	 * @return string
	 * @throws RandomException
	 */
	public function randomStyle(): string
	{
		$maxLength = count($this->allStyles) - 1;

		if($maxLength < 0) {
			throw new \RuntimeException("No available style to choose from.");
		}

		$randomIndex = random_int(0, $maxLength);
		return $this->allStyles[$randomIndex];
	}

	private function trimUrl(string $url)
	{

	}

	/* ------------------------------------------------------------------------- */

	public function getSeed(): string
	{
		return $this->seed;
	}

	public function setSeed(string $seed): void
	{
		$this->seed = $seed;
	}

	public function getChosenStyle(): ?string
	{
		return $this->chosenStyle;
	}

	public function setChosenStyle(?string $chosenStyle): void
	{
		$this->checkRequirements($chosenStyle, null);

		$this->chosenStyle = $chosenStyle;
	}

	public function getChosenReturnType(): ?string
	{
		return $this->chosenReturnType;
	}

	public function setChosenReturnType(?string $chosenReturnType): void
	{
		$this->checkRequirements(null, $chosenReturnType);

		$this->chosenReturnType = $chosenReturnType;
	}


}