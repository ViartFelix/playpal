<?php

namespace App\Twig;

use App\Classes\RandomAvatar;
use Random\RandomException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AvatarExtension extends AbstractExtension
{
	public function getFunctions(): array {
		return [
			new TwigFunction('random_icon', [$this, 'randomIconHandler'])
		];
	}

	/**
	 * Return an url to get a random avatar
	 * @param mixed|null $seed Seed for the image
	 * @return string
	 * @throws RandomException
	 */
	public function randomIconHandler(mixed $seed = null): string
	{
		//random seed if not present
		$seed ??= time() . uniqid(more_entropy: true);
		$safeSeed = md5($seed);

		$randomAvatarHandler = new RandomAvatar($safeSeed, returnType: "png");

		return $randomAvatarHandler->buildUrl();
	}
}