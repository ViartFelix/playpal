<?php

namespace App\Services;

use App\Interfaces\MetricServiceConstants;
use App\Interfaces\Roundable;

class MetricService implements MetricServiceConstants, Roundable
{
	/** @var int Default numbers of digits after the comma */
	private int $defaultRoundTo = 3;

	public function __construct(
		?int $defaultRoundTo = null
	) {
		$this->defaultRoundTo = $defaultRoundTo ?? 3;
	}

	/**
	 * km -> miles
	 * @param float|int $km
	 * @param int|null $roundTo
	 * @return float
	 */
	public function toMiles(float|int $km, ?int $roundTo = null): float
	{
		$result = $km * self::milesForOneKm;
		return $this->round($result, $roundTo);
	}

	/**
	 * km -> m
	 * @param float|int $km
	 * @param int|null $roundTo
	 * @return float
	 */
	public function toMeters(float|int $km, ?int $roundTo = null): float
	{
		$result = $km * 1000;
		return $this->round($result, $roundTo);
	}

	/**
	 * km -> Cm
	 * @param float|int $km
	 * @param int|null $roundTo
	 * @return float
	 */
	public function toCentimeters(float|int $km, ?int $roundTo = null): float
	{
		$result = $this->toMeters($km, $roundTo) * 1000;
		return $this->round($result, $roundTo);
	}

	/**
	 * km -> light years
	 * @param float|int $km
	 * @param int|null $roundTo
	 * @return float
	 */
	public function toLightYears(float|int $km, ?int $roundTo = null): float
	{
		$result = $km * $this::lightYearsForOneKm;
		return $this->round($result, $roundTo);
	}

	/**
	 * km -> feet
	 * @param float|int $km
	 * @param int|null $roundTo
	 * @return float
	 */
	public function toFeet(float|int $km, ?int $roundTo = null): float
	{
		$result = $km * $this::feetsForOneKm;
		return $this->round($result, $roundTo);
	}

	/**
	 * km -> standard hotdog (~= 15 cm in length)
	 * @param float|int $km
	 * @param int|null $roundTo
	 * @return float
	 */
	public function toHotDogs(float|int $km, ?int $roundTo = null): float
	{
		$result = $km * $this::hotDogsForOneKm;
		return $this->round($result, $roundTo);
	}

	/**
	 * km -> bernese mountain dog (1 bernese mountain dog ~= 115 cm from nose to tail)
	 * @param float|int $km
	 * @param int|null $roundTo
	 * @return float
	 */
	public function toBerneseMountainDogs(float|int $km, ?int $roundTo = null): float
	{
		$result = $km * $this::berneseMountainDogsForOneKm;
		return $this->round($result, $roundTo);
	}

	/**
	 * Returns an array of informations with the best suited metric unit for the given distance.
	 * Eg: If given 0.3Km, it will return something like "300 m"
	 * @param float|int $km
	 * @return array
	 */
	public function getAdaptedUnits(float|int $km): array
	{
		$cmResult = $this->toCentimeters($km);
		$meterResult = $this->toMeters($km);
		$lightYearResult = $this->toLightYears($km);

		//array containing informations on what unit best fits the distance given
		$unitsMap = [
			'none' => [
				"condition" => $meterResult <= 0,
				"value" => 0,
			],
			'cm' => [
				'condition' => $meterResult < 1,
				'short' => $this::shortCentimetersUnitValue,
				'long' => $this::longCentimetersUnitValue,
				'value' => $cmResult,
			],
			'm' => [
				'condition' => $km < 1,
				'short' => $this::shortMetersUnitValue,
				'long' => $this::longMetersUnitValue,
				'value' => $meterResult,
			],
			'km' => [
				'condition' => $lightYearResult < 1,
				'short' => $this::shortKilometersUnitValue,
				'long' => $this::longKilometersUnitValue,
				'value' => $km,
			],
			//default value
			'light_years' => [
				'condition' => true,
				'short' => $this::shortLightYearsUnitValue,
				'long' => $this::longLightYearsUnitValue,
				'value' => $lightYearResult,
			],
		];

		foreach ($unitsMap as $unitString => $unitProps) {
			if($unitProps["condition"]) {
				return $this->getAdaptedUnitsResultFromProps($unitProps);
			}
 		}

		throw new \LogicException("No valid unit found for the given distance.");
	}

	/**
	 * Returns an array of the results for the "getAdaptedUnits" method from the props given.
	 * @param array $unitProps
	 * @return array
	 */
	private function getAdaptedUnitsResultFromProps(array $unitProps): array
	{
		$shortUnit = array_key_exists("short", $unitProps)
			? $unitProps["short"] ?? null
			: null;

		$longUnit = array_key_exists("long", $unitProps)
			? $unitProps["long"] ?? null
			: null;

		$distanceValue = $unitProps["value"] ?? null;

		$shortDistance = "";
		$longDistance = "";

		//empty checks if value is not 0 and not null
		//if user is at the place
		if(empty($shortUnit) && empty($longUnit) && empty($distanceValue)) {
			$distanceValue = "You are here";
			$shortDistance = $distanceValue;
			$longDistance = $distanceValue;
		} else {
			//format strings for the "clean" distance
			$shortDistance = sprintf("%s %s", $unitProps["value"], $shortUnit);
			$longDistance = sprintf("%s %s", $unitProps["value"], $longUnit);
		}

		return [
			"unit" => [
				"short" => $shortUnit,
				"long" => $longUnit,
			],
			"value" => $distanceValue,
			//sentences on the result
			"details" => [
				"short" => $shortDistance,
				"long" => $longDistance,
			]
		];
	}

	/**
	 * Rounds a value.
	 * @param float|int $value
	 * @param int|null $roundTo
	 * @return float
	 */
	public function round(float|int $value, ?int $roundTo = null): float
	{
		return round($value, $this->getRoundTo($roundTo));
	}

	/**
	 * Returns how many digits after the comma a number should be rounded to
	 * (depending on the given param and default param)
	 * @param int|null $givenRoundTo
	 * @return int
	 */
	public function getRoundTo(?int $givenRoundTo = null): int
	{
		return $givenRoundTo ?? $this->defaultRoundTo;
	}

	/* --------------------------------------------------------- */

	public function getDefaultRoundTo(): int
	{
		return $this->defaultRoundTo;
	}

	public function setDefaultRoundTo(int $defaultRoundTo): void
	{
		$this->defaultRoundTo = $defaultRoundTo;
	}
}