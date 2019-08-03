<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends Controller
{

    function lucky_numbers($count = null){

        $numbers = array();
        $len = '';

        $count == null ? $len = 1 : $len = $count ;
        for($i=0; $i < $len; $i++){

            $numbers[] = random_int(0, 100);
        }
        $numberslist = implode(', ', $numbers);

        return $numberslist;
    }

    /**
     * @Route("/lucky/number", name="lucky")
     */
    public function number()
    {
        $number = $this->lucky_numbers();

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

    //####### Return en JSON ##########

    /**
     * @Route("/lucky/number/json")
     */
    public function apiNumberAction(){

        $data = ['lucky' => $this->lucky_numbers()];

        //return new Response(json_encode($data));
        return new JsonResponse($data);
    }

    /**
     * @Route("/lucky/number/{count}")
     * @param $count
     * @return Response
     */
    public function numbersAction($count){
        $nbr = $this->lucky_numbers($count);
        return new Response("<html><body> <h1>Lucky numbers : <sapn style='color:blue'>$nbr</sapn></h1></body></html>");
    }

    // ######Rendering a Template (with the Service Container)#####################

    

}
