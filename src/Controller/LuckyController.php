<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{
    /**
     * @Route("/lucky/number", name="lucky")
     */
    public function number()
    {
        $number = random_int(0, 100);

        /**
        //Premier methode
        return new Response("<htm><body>
               <p>Votre numero de chance est $number</p>
        </body></htm>");
        **/

        //Deuxieme methodes
        return $this->render('lucky/index.html.twig', [
            'controller_name' => 'LuckyController',
            "number" => $number,
        ]);
    }
}
