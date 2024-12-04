<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Participant;
use App\Traits\DbHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participant>
 */
class ParticipantRepository extends ServiceEntityRepository
{
    use DbHelperTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

	public function isParticipantEmailAvailable(Participant $participant): bool
	{
		/** @var Event $targetEvent */
		$targetEvent = $participant->getEvent();
		$allEventParticipants = $targetEvent->getParticipants();

		foreach ($allEventParticipants as $eventParticipant) {
			if($eventParticipant->getEmail() === $participant->getEmail()) {
				return false;
			}
		}

		return true;
	}

	public function isParticipantNameAvailable(Participant $participant): bool
	{
		/** @var Event $targetEvent */
		$targetEvent = $participant->getEvent();
		$allEventParticipants = $targetEvent->getParticipants();

		foreach ($allEventParticipants as $eventParticipant) {
			if($eventParticipant->getName() === $participant->getName()) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns true if the participant is not already registered to an event.
	 * Checks if the mail or the name are took present from the participants
	 * @param Participant $participant
	 * @return bool
	 */
	public function isParticipantRegistered(Participant $participant): bool
	{
		/** @var Event $targetEvent */
	   $targetEvent = $participant->getEvent();
	   $allEventParticipants = $targetEvent->getParticipants();

	   foreach ($allEventParticipants as $eventParticipant) {
		   $isNameTaken = $eventParticipant->getName() === $participant->getName();
		   $isMailTaken = $eventParticipant->getEmail() === $participant->getEmail();

		   if($isNameTaken || $isMailTaken) {
			   return false;
		   }
	   }

	   return true;
	}
}
