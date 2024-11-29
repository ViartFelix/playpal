<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture
{
    /**
     * An array containing random sports events
     * @var array
     */
    private array $fixturesEvents = [
        "International Cricket Tournament", "Roller Derby Championship", "Marathon Trail Race", "Beach Volleyball Cup",
        "Table Tennis Masters Open", "World Cup of Soccer", "Mountain Biking Challenge", "Sailing Regatta Championship",
        "International Chess Olympiad", "Rock Climbing Competition", "Parkour Freestyle Championship",
        "CrossFit Fitness Challenge", "Ultimate Frisbee League Finals", "High-Altitude Skiing Cup",
        "Formula One Grand Prix", "Badminton World Tour Finals", "Triathlon Series Championship",
        "Professional Wrestling Gala", "Kabaddi Pro League", "Archery World Cup", "Horse Racing Derby",
        "Rugby World Series", "Ice Hockey Winter Classic", "Darts World Championship", "Lacrosse Invitational Cup",
        "Snooker Grand Masters", "Track and Field National Meet", "Cycling Tour de Hills", "Snowboarding Open Challenge",
        "Polo International Match", "Surfing World Championships", "Boxing Heavyweight Title Fight",
        "Esports Battle Royale Tournament", "Rowing Regatta Open", "Skateboarding Street Finals",
        "Powerlifting National Championship", "Cricket T20 Blitz", "Softball National Championship",
        "Handball Continental Cup", "Squash International Open", "Swimming World Relay Challenge",
        "Fencing World Duel Championship", "Water Polo Invitational", "American Football Super Bowl",
        "Gymnastics Artistic Showcase", "Field Hockey Continental League", "Judo Grand Slam Event",
        "Bouldering World Series", "Tennis Grand Slam Final", "Mixed Martial Arts (MMA) Championship"
    ];

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $remainingEvents = $this->fixturesEvents;

        foreach (range(1, 25) as $randomEventIndex) {
            $event = new Event();

            //random distinct event
            $randomIndex = array_rand($remainingEvents);
            $targetEvent = $remainingEvents[$randomIndex];
            $event->setName($targetEvent);

            $eventDateMutable = $faker->dateTimeBetween("now", "+1 year");
            $event->setDate(\DateTimeImmutable::createFromMutable($eventDateMutable));

            //random latitude and longitude
            $event->setLatitude($faker->latitude());
            $event->setLongitude($faker->longitude());

            //and delete the random event to have unique events to choose from
            unset($remainingEvents[$randomIndex]);

            $manager->persist($event);
        }

        $manager->flush();
    }
}