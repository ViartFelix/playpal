<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\CreateNewEventType;
use App\Repository\EventRepository;
use App\Services\EventsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/events", name: "events.")]
class EventController extends AbstractController
{
    private EventRepository $eventRepository;
	private EntityManagerInterface $entityManager;

    public function __construct(
        EventRepository $eventRepository,
		EntityManagerInterface $entityManager
    ) {
        $this->eventRepository = $eventRepository;
		$this->entityManager = $entityManager;
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
    public function viewEvent(int|Event $id): Response
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

		return $this->render("events/view.html.twig", [
			"event" => $event,
			"participants" => $eventParticipants
		]);
    }

	/**
	 * Route and form for a new event
	 * @param Request $request
	 * @return Response
	 */
	#[Route("/new", name: "new")]
	public function newEvent(Request $request): Response
	{
		$event = new Event();

		$form = $this->createForm(CreateNewEventType::class, $event);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			/** @var Event $formEvent */
			$formEvent = $form->getData();

			$this->entityManager->persist($formEvent);
			$this->entityManager->flush();

			$this->addFlash("success", sprintf(
				"The event '%s' has been created for the date %s",
				$formEvent->getName(), $formEvent->getDate()->format('d/m/Y')
			));

			return $this->redirectToRoute("events.single", ["id" => $event->getId()]);
		}

		return $this->renderNewEventForm($form);
	}

	/**
	 * Returns a render of the new event form
	 * @param FormInterface $form The "CreateNewEventType" form
	 * @return Response
	 */
	private function renderNewEventForm(FormInterface $form): Response
	{
		return $this->render("events/new.html.twig", [
			"form" => $form->createView()
		]);
	}

	/**
	 * API Route to calculate the distance to an event.
	 */
	#[Route("/{event}/distance", name: "api.calculator", requirements: [
		"event" => '\d+'
	])]
	public function calculateDistanceToEvent(Event $event, Request $request, EventsService $eventsService): JsonResponse
	{
		//first check if all params are present
		$requestLatitude = $request->get("lat", null) ?? $request->get("latitude", null);
		$requestLongitude = $request->get("lon", null) ?? $request->get("longitude", null);

		$eventsService->checkLatitudeLongitude($requestLatitude, $requestLongitude);

		$givenLatitude = (float) $requestLatitude;
		$givenLongitude = (float) $requestLongitude;

		//and we calculate the distance in km
		$distance = $eventsService->calculateDistance(
			$event->getLatitude(),
			$event->getLongitude(),
			$givenLatitude,
			$givenLongitude
		);

		//additional data
		$bonusData = $eventsService->getMetricConversions(
			$distance, $request->get("digits", null)
		);

		return $this->json([
			"message" => "Distance has been calculated.",
			"distance" => $distance,
			"bonus" => $bonusData
		], 200);
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
