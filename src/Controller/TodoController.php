<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]
class TodoController extends AbstractController
{
    #[Route('/', name: 'todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        // Afficher notre tableau todo

        // Sinon, je l'initialise
        if (!$session->has('todos')) {
            $todos = [
                'achat' => 'acheter cle usb',
                'cours' => 'Finaliser mon cours',
                'correction' => 'corriger mes examens'
            ];
            $session->set('todos', $todos);
            $this->addFlash('info', "La liste des todos vient d'être initialisée");
        }

        // Si j'ai mon tableau dans la session

        return $this->render('todo/index.html.twig');
    }

    #[Route('/add/{name?sf6}/{content?test}', name: 'todo.add')]
    public function addTodo(Request $request, $name, $content): RedirectResponse
    {
        $session = $request->getSession();
        if ($session->has('todos'))
        {
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                $this->addFlash('error', "Le todos id $name existe déjà");
            } else {
                $todos[$name] = $content;
                $this->addFlash('success', "Le todos id $name a été ajouté avec succès");
                $session->set('todos', $todos);

            }
        } else {
            $this->addFlash('error', "La liste des todos n'est pas initialisée");
        }

        return $this->redirectToRoute('todo');
    }

    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request, $name, $content): RedirectResponse
    {
        $session = $request->getSession();
        if ($session->has('todos'))
        {
            $todos = $session->get('todos');
            if (!isset($todos[$name])) {
                $this->addFlash('error', "Le todos id $name n'existe pas");
            } else {
                $todos[$name] = $content;
                $this->addFlash('success', "Le todos id $name a été modifie avec succès");
                $session->set('todos', $todos);

            }
        } else {
            $this->addFlash('error', "La liste des todos n'est pas initialisée");
        }

        return $this->redirectToRoute('todo');
    }

    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo(Request $request, $name): RedirectResponse
    {
        $session = $request->getSession();
        if ($session->has('todos'))
        {
            $todos = $session->get('todos');
            if (!isset($todos[$name])) {
                $this->addFlash('error', "Le todos id $name n'existe pas");
            } else {
                unset($todos[$name]);
                $this->addFlash('success', "Le todos id $name a été supprime avec succès");
                $session->set('todos', $todos);

            }
        } else {
            $this->addFlash('error', "La liste des todos n'est pas initialisée");
        }

        return $this->redirectToRoute('todo');
    }

    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $session->remove('todos');

        return $this->redirectToRoute('todo');
    }


}
