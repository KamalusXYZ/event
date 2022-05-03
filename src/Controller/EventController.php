<?php

namespace App\Controller;

use App\Entity\Event;

use App\Entity\UserListByEvents;
use App\Form\EventType;

use App\Repository\EventRepository;
use App\Repository\UserListByEventsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->add($event);
            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event, UserListByEventsRepository $userList, $id, EventRepository $eventRepository): Response
    {
        $views = $event->getViews();
        $event->setViews($views+1);
        $eventRepository->add($event);


        return $this->render('event/show.html.twig', [
            'event' => $event,
            'userlist'=>$userList->findBy(
                ['events'=>$id]
            )

        ]);

    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->add($event);
            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event);
        }

        return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/signup', name: 'app_signup')]
    public function signUpUser(UserListByEventsRepository $userListByEventsRepository, Event $event): Response
    {
        $user = $this->getUser();

        if(!$user) return $this->redirectToRoute('app_login');


        if($event->isUserSignedUp($user)){
            $subscription =  $userListByEventsRepository->findOneBy(
                ['users'=>$user, 'events'=>$event]
            );
            $userListByEventsRepository->remove($subscription);
            return $this->redirectToRoute('app_main');

        }

        $subscription = new UserListByEvents();
        $subscription->setUsers($user)->setEvents($event);
        $userListByEventsRepository->add($subscription);
        return $this->redirectToRoute('app_main');




    }
}



