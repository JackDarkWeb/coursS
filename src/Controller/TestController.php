<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/user/{id?}", name="show", requirements={"id" = "\d+"})
     */
    public function show($id){
        if($id == null){

            return new Response("Tous les users", 200);
        }
        return new Response($id, 200);
    }
}