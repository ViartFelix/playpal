<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Form\AddParticipantToEventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/participants", name: "participants.")]
class ParticipantController extends AbstractController
{
    private EventRepository $eventRepository;
	private EntityManagerInterface $entityManager;

    public function __construct(
        EventRepository $eventRepository,
		EntityManagerInterface $entityManager
    )
    {
        $this->eventRepository = $eventRepository;
		$this->entityManager = $entityManager;
    }

	/**
	 * Add a participant to an event.
	 * @param int|Event $id
	 * @param Request $request
	 * @return JsonResponse
	 */
    #[Route('/add/{id}', name: 'add', requirements: [
        "id" => '\d+'
    ])]
    public function addParticipant(int|Event $id, Request $request): Response
    {
        $event = is_int($id)
            ? $this->eventRepository->find($id)
            : $id;

        if(is_null($event)) {
            throw $this->createNotFoundException(
                sprintf("The event #%s does not exist.", $id)
            );
        }

		$participant = new Participant();
		$participant->setEvent($event);

		$form = $this->createForm(AddParticipantToEventType::class, $participant);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			/** @var Participant $formParticipant */
			$formParticipant = $form->getData();

			$this->entityManager->persist($formParticipant);
			$this->entityManager->flush();

			return $this->redirectToRoute("events.single", ["id" => $event->getId()]);
		}

        return $this->render("participant/new.html.twig", [
			"form" => $form
		]);
    }
}
