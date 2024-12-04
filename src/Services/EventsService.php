<?php

namespace App\Services;

use App\interfaces\EventsServiceInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EventsService implements EventsServiceInterface
{
	/** @var float Earth's radius in Km */
	private static float $earthRadius = 6378.0;

	private static float $maxLatitude = 90.0;

	private static float $minLatitude = -90.0;

	private static float $maxLongitude = 180.0;

	private static float $minLongitude = -180.0;

	private MetricService $metricService;

	public function __construct(?MetricService $metricService = null)
	{
		$this->metricService = $metricService ?? new MetricService();
	}

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

	public function checkLatitudeLongitude(int|string|null $requestLatitude, int|string|null $requestLongitude): static
	{
		//first check if all required params are present
		if(is_null($requestLatitude) || is_null($requestLongitude)) {

			//get the param (lat or lon) that makes this request unprocessable
			$requestMissingParam = is_null($requestLatitude)
				? "latitude"
				: "longitude";

			throw new BadRequestHttpException(
				sprintf("The %s is missing from the GET parameters.", $requestMissingParam)
			);
		}

		//we check if the params are numbers
		if(!is_numeric($requestLatitude) || !is_numeric($requestLongitude)) {

			//get the param (lat or lon) that makes this request unprocessable
			$requestNonNumericParam = !is_numeric($requestLatitude)
				? "latitude"
				: "longitude";

			throw new BadRequestException(
				sprintf("The provided %s is not a number.", $requestNonNumericParam)
			);
		}

		//finally, we check if the latitude and longitude are in the right ranges
		$longitude = (float) $requestLongitude;
		$latitude = (float) $requestLatitude;

		$isLatitudeInRange = $this->isLatitudeInRange($latitude);
		$isLongitudeInRange = $this->isLongitudeInRange($longitude);

		if(!$isLatitudeInRange || !$isLongitudeInRange) {
			//what coordinate (lat or lon) make the request unprocessable
			$requestNonInRangeParam = "";
			$requestGivenParam = "";

			//min and max of the coordinate
			$requestParamMinRange = "";
			$requestParamMaxRange = "";

			if(!$isLatitudeInRange) {
				$requestNonInRangeParam = "latitude";
				$requestGivenParam = $requestLatitude;
				$requestParamMinRange = self::$minLatitude;
				$requestParamMaxRange = self::$maxLatitude;
			} else {
				$requestNonInRangeParam = "longitude";
				$requestGivenParam = $requestLongitude;
				$requestParamMinRange = self::$minLongitude;
				$requestParamMaxRange = self::$maxLongitude;
			}

			throw new BadRequestException(
				sprintf(
					"The provided %s ('%s') is not in the correct range: %s to %s.",
					$requestNonInRangeParam, $requestGivenParam, $requestParamMinRange, $requestParamMaxRange
				)
			);
		}

		return $this;
	}

	/**
	 * Returns true if the provided latitude is in range or false otherwise
	 * @param float|int $latitude
	 * @return bool
	 */
	protected function isLatitudeInRange(float|int $latitude): bool
	{
		return self::$minLatitude <= $latitude && $latitude <= self::$maxLatitude;
	}

	/**
	 * Returns true if the provided longitude is in range or false otherwise
	 * @param float|int $longitude
	 * @return bool
	 */
	protected function isLongitudeInRange(float|int $longitude): bool
	{
		return self::$minLongitude <= $longitude && $longitude <= self::$maxLongitude;
	}


	public function getMetricConversions(int|float $value, ?int $roundedAt = null): array
	{
		//convert the value; it'll be simpler later
		$floatValue = (float) $value;

		//default round to
		$roundedAt ??= 9;
		$this->metricService->setDefaultRoundTo($roundedAt);

		$metricResults = [
			"km" => $floatValue,
			"m" => $this->metricService->toMeters($floatValue),
			"cm" => $this->metricService->toCentimeters($floatValue),
		];

		$imperialResults = [
			"miles" => $this->metricService->toMiles($floatValue),
			"feets" => $this->metricService->toFeet($floatValue)
		];

		$otherUnits = [
			"ly" => $this->metricService->toLightYears($floatValue),
			"hot_dogs" => $this->metricService->toHotDogs($floatValue),
			"bernese_mountain_dogs" => $this->metricService->toBerneseMountainDogs($floatValue),
		];

		return [
			"metric" => $metricResults,
			"imperial" => $imperialResults,
			"other" => $otherUnits,
			"clean" => $this->metricService->getAdaptedUnits($floatValue)
		];
	}
}