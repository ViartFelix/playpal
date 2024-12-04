<?php

namespace App\interfaces;

interface MetricServiceConstants
{
	/** @var float How many miles for a km */
	const milesForOneKm = 0.6213712;

	/** @var float How many light years for a km */
	const lightYearsForOneKm = 1.057004e-13;

	/** @var float How much hotdogs for a km */
	const hotDogsForOneKm  = 6666.6666;

	/** @var float How much feets for a km */
	const feetsForOneKm = 3280.84;

	/** @var float How much bernese mountain dogs for a km */
	const berneseMountainDogsForOneKm = 869.565217391;


	/** @var string "Clean" value for the "km" unit */
	const longLightYearsUnitValue = "Light years";

	/** @var string "Clean" value for the "km" unit */
	const longKilometersUnitValue = "Kilometers";

	/** @var string "Clean" value for the "m" unit */
	const longMetersUnitValue = "Meters";

	/** @var string "Clean" value for the "cm" unit */
	const longCentimetersUnitValue = "Centimeters";

	/** @var string "Short" value for the "cm" unit */
	const shortLightYearsUnitValue = "ly";

	/** @var string "Short" value for the "km" unit */
	const shortKilometersUnitValue = "km";

	/** @var string "Short" value for the "m" unit */
	const shortMetersUnitValue = "m";

	/** @var string "Short" value for the "cm" unit */
	const shortCentimetersUnitValue = "cm";

}