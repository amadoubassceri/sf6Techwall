<?php

namespace App\Controller;

use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{

    #[Route('/template', name: 'template')]
    public function template(): Response
    {
        return $this->render('template.html.twig');
    }

    #[Route('/first', name: 'first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig',
        [
            'name' =>  ' amadou',
            'firstname' => 'Bass'
            ]);
    }


  //  #[Route('/sayHello/{name}/{firstname}', name: 'say.hello')
    public function sayHello(\Symfony\Component\HttpFoundation\Request $request,$name,$firstname): Response
    {


        return $this->render('first/hello.html.twig',[
            'nom'=> $name,
            'prenom' => $firstname

        ]);
    }
}
