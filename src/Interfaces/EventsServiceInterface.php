<?php

namespace App\Interfaces;

interface EventsServiceInterface
{
	/**
	 * Returns an array of metric-related measurements relative to the given value.
	 * @param float|int $value Value in Km
	 * @param int|null $roundedAt How much digits in the results after the comma.
	 * @return array{
	 *     metric: array{
	 *     		km: float,
	 *    		m: float,
	 *     		cm: float,
	 *     },
	 *     imperial: array{
	 *         	miles: float,
	 *     		feets: float,
	 *     },
	 *     other: array{
	 *         ly: float
	 *     },
	 *     clean: array{
	 *         distance: float,
	 *         unit: string
	 *     }
	 * }
	 */
	public function getMetricConversions(int|float $value, ?int $roundedAt = null): array;

	/**
	 * Checks if the provided latitudes and longitudes are valid.
	 * If not, throws the appropriate HTTP exception.
	 * @param int|string|null $requestLatitude
	 * @param int|string|null $requestLongitude
	 * @return static
	 */
	public function checkLatitudeLongitude(int|string|null $requestLatitude, int|string|null $requestLongitude);

	/**
	 * Calculates the distance between two points on earth using the Haversine formula. <br/>
	 * **Caution**: The latitudes and longitudes must be in degrees.
	 * @param float|int $latOne
	 * @param float|int $lonOne
	 * @param float|int $latTwo
	 * @param float|int $lonTwo
	 * @return float
	 */
	public function calculateDistance(float|int $latOne, float|int $lonOne, float|int $latTwo, float|int $lonTwo): float;
}