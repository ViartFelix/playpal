<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/events")]
class EventController extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(
        EventRepository $eventRepository
    )
    {
        $this->eventRepository = $eventRepository;
    }


    /**
     * Lists all available events
     * @return JsonResponse
     */
    #[Route("/", name: "events.all")]
    public function listEvents(): JsonResponse
    {
        $allEvents = $this->eventRepository->findAll();
        return $this->json($allEvents);
    }

    /**
     * Returns the details for an event with a list of it's participants
     * @param int $id
     * @return JsonResponse
     */
    #[Route("/{id}", name: "events.single", requirements: [
        "id" => '\d+'
    ])]
    public function viewEvent(int $id): JsonResponse
    {
        /** @var Event|null $targetEvent */
        $event = $this->eventRepository->find($id);

        if(is_null($event)) {
            throw $this->createNotFoundException(
                sprintf("The event #%s does not exist.", $id)
            );
        }

        $eventParticipants = $event->getParticipants();

        return $this->json([
            "event" => $event,
            "participants" => $eventParticipants
        ]);
    }
}
