<?php
// src/Controller/AuthController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(Request $req) {
        
        return $this->render('index/index.html.twig', [
            "current_date" => date("d/m/Y H:i:s")
        ]);
    }

    /**
     * @Route("/checkCookie", name="app_cookie")
     */
     public function checkCookie(Request $request) {
        // On récupère le cookie s'il existe
        $cookie = $request->cookies->get('visited');
        $value = 0;

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // On récupère la valeur du cookie
        if(!empty($cookie)) {
            $value = $cookie;
        }

        if(!empty($cookie) || $request->request->get("accept") == true) {
            // On incrémente la valeur du cookie
            $value++;
            // On retourne le cookie
            $cookie = new Cookie("visited", $value, 0);
            $response->headers->setCookie($cookie);
        }

        // On retourne la réponse
        $response->setContent(json_encode([
            'visited' => $value,
        ]));

        return $response;
    }

}