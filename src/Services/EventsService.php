<?php

namespace App\Services;

class EventsService
{
	/** @var float Earth's radius in Km */
	private static float $earthRadius = 6378.0;

	/**
	 * Calculates the distance between two points on earth using the Haversine formula. <br/>
	 * **Caution**: The latitudes and longitudes must be in degrees.
	 * @param float|int $latOne
	 * @param float|int $lonOne
	 * @param float|int $latTwo
	 * @param float|int $lonTwo
	 * @return float
	 */
	public function calculateDistance(float|int $latOne, float|int $lonOne, float|int $latTwo, float|int $lonTwo): float
	{
		$latitudeOneRad = deg2rad($latOne);
		$longitudeOneRad = deg2rad($lonOne);

		$latitudeTwoRad = deg2rad($latTwo);
		$longitudeTwoRad = deg2rad($lonTwo);

		$haversineFormula = $this->applyHaversineFormula($latitudeOneRad, $longitudeOneRad, $latitudeTwoRad, $longitudeTwoRad);
		$angularResult = $this->applyAngularDistance($haversineFormula);

		return $this->angularDistanceToLinear($angularResult);
	}

	/**
	 * Applies the Haversine Formula to the numbers given. <br/>
	 *  **Caution**: The latitudes and longitudes must be in radiants.
	 * @param float $latOne
	 * @param float $lonOne
	 * @param float $latTwo
	 * @param float $lonTwo
	 * @return float
	 */
	protected function applyHaversineFormula(float $latOne, float $lonOne, float $latTwo, float $lonTwo): float
	{
		$latitudeDiff = $latOne - $latTwo;
		$longitudeDiff = $lonOne - $lonTwo;

		//get north-south difference between latitudes (without earth's angle)
		$northSouthDiff = sin($latitudeDiff / 2) ** 2;

		//same for east-west between longitudes
		$eastWestDiff = sin($longitudeDiff / 2) ** 2;

		//get the earth's curvature (latitude)
		$latitudeCurvature = cos($latOne) * cos($latTwo);

		//and we stitch all that together
		return $northSouthDiff + $latitudeCurvature * $eastWestDiff;
	}

	/**
	 * Applies the angular distance after applying the Haversine Formula. (Angular distance of the longitudes)
	 * @param float $formulaResult
	 * @return float
	 */
	protected function applyAngularDistance(float $formulaResult): float
	{
		return 2 * atan2(
			sqrt($formulaResult),
			sqrt(1 - $formulaResult),
		);
	}

	/**
	 * Converts angular distance to a linear distance in Km using earth's radius (in Km)
	 * @param float $angularDistance
	 * @return float
	 */
	protected function angularDistanceToLinear(float $angularDistance): float
	{
		return $angularDistance * self::$earthRadius;
	}
}