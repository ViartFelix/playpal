<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Repository\EventRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ParticipantFixtures extends Fixture
{
    private EventRepository $eventRepository;

    public function __construct(
        EventRepository $eventRepository
    )
    {
        $this->eventRepository = $eventRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (range(1, 150) as $randomParticipantIndex) {
            $participant = new Participant();

            $participantName = $faker->name();
            //cut the string if too long for db
            $participant->setName(
                substr($participantName, 0, 50)
            );

            $participant->setEmail($faker->safeEmail());

            //random event from the DB
            $targetEvent = $this->eventRepository->getRandomEntity();
            $participant->setEvent($targetEvent);

            $manager->persist($participant);
        }

        $manager->flush();
    }
}