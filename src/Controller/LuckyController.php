<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends Controller
{

    public function lucky_numbers($len = null){
        $count = '';
        $numbers = array();

        $len == null? $count = 1 : $count = $len;
        for($i = 0; $i < $count; $i++){
            $numbers[] = random_int(0, 100);
        }
        $numberslist = implode(', ', $numbers);
        return $numberslist;
    }
    public function data($id = null){
      $users = [
          1 => ["name" => 'Jack', 'age'=> 25],
          ["name" => 'Paul', 'age' => 58],
          ["name" => "Rosine", 'age' => 29],
          ["name" => 'Rachelle', 'age' => 30]
      ];
      return $id == null ? $users : $users[$id];
    }

    /**
     * @Route("/lucky/number")
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

    // ##########E# Creating a JSON Response #########

    /**
     * @Route("/lucky/number/json")
     * @return Reponse
     */
    public function apiNumbersAction(){
        // 1 ere methode
        $data = ["lucky_number" => $this->lucky_numbers()];
        //$data = json_encode($data);

        //return new Response("<html><body> <h3>Lucky number is  \r $data</h3></body></html>", 200);

        // 2 eme Methode

        return new JsonResponse( $data);
    }

    // ###############  Dynamic URL Patterns: /lucky/number/{count} #############

    /*
    /**
     * @param $count
     * @return Response
     * @Route("lucky/number/{count}")

    public function numberAction($count){

        $numbers = $this->lucky_numbers($count);

        return new Response("<html><body> <h3>Ours lucky number are   <span style='font-weight: bold; color: #916319'>$numbers</span></h3></body></html>");
    }
*/
    /**
     * @param $id
     * @return Response
     * @Route("/user/{id}")
     */
    public function show($id){
        $user = $this->data($id);
        $user = (object)$user;
        return new Response("<html><body><table><thead><tr><th>Name</th><th>Age</th></tr></thead><tbody><tr><td>$user->name</td><td>$user->age</td></tr></tbody></table></body></html>");
    }


    // ################   Rendering a Template (with the Service Container) #########

    /**
     * @param $count
     * @return Response
     * @Route("lucky/number/{count}")
     */
    public function numberAction($count)
    {

        $numbers = $this->lucky_numbers($count);

        return $this->render("lucky/lucky.html.twig", [
            "lucky" => $numbers
        ]);
    }
}
