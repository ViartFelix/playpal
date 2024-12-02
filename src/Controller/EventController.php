<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/events", name: "events.")]
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
     * Lists all available events.
     * @return Response
     */
    #[Route("/", name: "all")]
    public function listEvents(): Response
    {
        $allEvents = $this->eventRepository->findAll();

        return $this->render("events/list.html.twig", [
            "allEvents" => $allEvents
        ]);
    }

    /**
     * Returns the details for an event with a list of it's participants
     * @param int|Event $id
     * @return JsonResponse
     */
    #[Route("/{id}", name: "single", requirements: [
        "id" => '\d+'
    ])]
    public function viewEvent(int|Event $id): JsonResponse
    {
        /** @var Event|null $targetEvent */
        $event = is_int($id)
            ? $this->eventRepository->find($id)
            : $id;

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

    /**
     * Adds a participant to an event. This request is forwarded to ParticipantController::addParticipant()
     * @see ParticipantController::addParticipant()
     * @param int|Event $event
     * @return Response
     *
     */
    #[Route("/{event}/participants/add", name: "participant.add", requirements: [
        "event" => '\d+'
    ])]
    public function addParticipant(int|Event $event): Response
    {
        return $this->forward(
            ParticipantController::class . "::addParticipant",
            ["id" => $event]
        );
    }
}
