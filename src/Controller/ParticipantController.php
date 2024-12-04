<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Form\AddParticipantToEventType;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/participants", name: "participants.")]
class ParticipantController extends AbstractController
{
    private EventRepository $eventRepository;
	private EntityManagerInterface $entityManager;
	private ParticipantRepository $participantRepository;

    public function __construct(
        EventRepository $eventRepository,
		EntityManagerInterface $entityManager,
		ParticipantRepository $participantRepository,
    ) {
        $this->eventRepository = $eventRepository;
		$this->entityManager = $entityManager;
		$this->participantRepository = $participantRepository;
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

			$isNameAvailable = $this->participantRepository->isParticipantNameAvailable($formParticipant);
			$isEmailAvailable = $this->participantRepository->isParticipantEmailAvailable($formParticipant);

			if(!$isNameAvailable || !$isEmailAvailable) {
				//add errors to the form

				if(!$isNameAvailable) {
					$form->get("name")->addError(new FormError("This name is already taken."));
				}

				if(!$isEmailAvailable) {
					$form->get("email")->addError(new FormError("This email is already taken."));
				}

				return $this->renderAddParticipantForm($form);
			} else {
				$this->entityManager->persist($formParticipant);
				$this->entityManager->flush();

				$this->addFlash("success", sprintf(
					"The participant '%s' has been added for the event '%s'",
					$formParticipant->getName(), $formParticipant->getEvent()->getName(),
				));

				return $this->redirectToRoute("events.single", ["id" => $event->getId()]);
			}
		}

        return $this->renderAddParticipantForm($form);
    }

	/**
	 * Returns a render of the new participant to an event form
	 * @param FormInterface $form The "AddParticipantToEventType" form
	 * @return Response
	 */
	private function renderAddParticipantForm(FormInterface $form): Response
	{
		return $this->render("participant/new.html.twig", [
			"form" => $form->createView()
		]);
	}
}
