<?php

namespace App\interfaces;

interface Roundable
{
	/**
	 * Rounds a value.
	 * @param float|int $value
	 * @param int|null $roundTo
	 * @return float
	 */
	public function round(float|int $value, ?int $roundTo = null): float;

	/**
	 * Returns how many digits after the comma a number should be rounded to
	 * (depending on the given param and default param)
	 * @param int|null $givenRoundTo
	 * @return int
	 */
	public function getRoundTo(?int $givenRoundTo = null): int;
}