<?php

namespace App\Controller;



use App\Entity\Product;
use App\Repository\ProductRepository;
use http\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        if(count($this->data()) < $id){

            throw $this->createNotFoundException("Error 404 the page not found!");
        }

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

    //#############  The Request object as a Controller Argument ###########

    /**
     * @param Request $request
     * @Route("get/request")
     */
    public function getRequestion(Request  $request){

        $page = $request->query->get("page", 5);
        dd($page);
    }

    // ###################   Managing the Session #########

    /**
     * @param Request $request
     * @return Response
     * @Route("/session/get/{name}")
     */
    public function managingSession(Request $request, $name){

        $session = $request->getSession();

        $session->set('foo', 'bar');
        $foo = $session->get("foo");
        setcookie("test", "Jack", time() + 200);

        //$ip = $request->getClientIp();

        if(!isset($_COOKIE['name'])){

            setcookie('name', $name, time() + 50);

            return new Response("Bienvenu $name");

        }else{

            $get = $_COOKIE['name'];
            return new Response("Vous avez deja visite ce site $get");
        }
    }

    // ###### Flash Messages #####

    /**
     * @param Request $request
     * @return Response
     * @Route("/message/flash")
     */
    public function messageFlash(Request $request){
        $file = $request->files->get("file");
        dump($file);
        $this->addFlash("info", "Tout est accompli au nom de Jesus");
        return $this->render("flashmsg.html.twig");
    }

    //############## The Request and Response Object ############
    public function indexAction(Request $request){
        $request->isXmlHttpRequest(); // is it an Ajax request

        $request->getPreferredLanguage(array("fr", "en"));

        //récupérer les variables GET et POST respectivement
        $request->query->get("page");
        $request->request->get("page");

        // retrieve SERVER variables
        $request->server->get('HTTP_HOST');

        // retrieves an instance of UploadedFile identified by foo
        $request->files->get('foo');

        // retrieve a COOKIE value
        $request->cookies->get('PHPSESSID');
    }

    // ############## Persisting Objects to the Database #######################

    /**
     * @return Response
     * @Route("/product/create")
     */
    public function createAction(){
        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(19.99);
        $product->setDescription('Ergonomic and stylish');

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        $this->addFlash("create", "The product " .$product->getId(). " has been created");
        return $this->render("create.html.twig");
    }

    /**
     * @param $productId
     * @return Response
     * @Route("/product/{productId}")
     */
    public function showAction(ProductRepository $repository, $productId){

        /*$product = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->find($productId);
*/
        $product = $repository->find($productId);

        /*
        // dynamic method names to find a single product based on a column value
        $product = $repository->findOneById($productId);

        $product = $repository->findOneByName('Keyboard');

       // dynamic method names to find a group of products based on a column value
        $products = $repository->findByPrice(19.99);
        */

        if(!$product){
            $this->addFlash('notice','No product found for id '.$productId);
            return $this->redirectToRoute("products");
        }

        return $this->render("show.html.twig",[
            'product' => $product,
        ]);
    }

    /**
     * @param ProductRepository $repository
     * @return Response
     * @Route("/products", name="products")
     */
    public function index(ProductRepository $repository){

        $products = $repository->findAll();
        return $this->render("index.html.twig",[
            'products' => $products,
        ]);
    }

    /**
     * @param ProductRepository $repository
     * @param $productId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/update/{productId}")
     */
    public function updateAction(ProductRepository $repository, $productId){
        $em = $this->getDoctrine()->getManager();
        $update = $em->$repository->find($productId);

        $update->setPrice(12);
        $em->flush();

        if(!$update){
            $this->addFlash('notice','No product not found for id '.$productId);
            return $this->redirectToRoute("products");
        }
        $this->addFlash('update', 'The product '.$productId. ' has been updated');
        return $this->redirectToRoute('products');
    }

}
