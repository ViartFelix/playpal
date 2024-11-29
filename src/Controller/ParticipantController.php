<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/participants", name: "participants.")]
class ParticipantController extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(
        EventRepository $eventRepository
    )
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Add a participant to an event.
     * @param int|Event $id
     * @return JsonResponse
     */
    #[Route('/add/{id}', name: 'add', requirements: [
        "id" => '\d+'
    ])]
    public function addParticipant(int|Event $id): JsonResponse
    {
        $event = is_int($id)
            ? $this->eventRepository->find($id)
            : $id;

        if(is_null($event)) {
            throw $this->createNotFoundException(
                sprintf("The event #%s does not exist.", $id)
            );
        }

        //TODO: add form and handle adding participant

        return $this->json([
            "message" => "Created successfully !"
        ], 201);
    }
}
