<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Message; //Pour pouvoir utiliser les méthodes de messages
use App\Entity\Livredor; //Pour pouvoir utiliser les méthodes de livredor


class LivreOrController extends AbstractController {

    /**
     * @Route("/livreor", name="app_livreor")
     */
    public function livreor(Request $req, string $livredorFile)
    {
        $errors = null; // Notre réponse en cas d'erreure
        $success = false; // Notre variable si le message est bien écrit
        $livredor = new Livredor($livredorFile);

        if($req->getMethod() == "POST") {
            $nom = $req->request->get('nom');
            $message = $req ->request->get('message');

            if(!empty($nom) && !empty($message)){
                $messages = new message($_POST['nom'], $_POST['message']);
                if ($messages->isValid()) { // Si le message est valide en utilisant la méthode dans message
                    $livredor->addMessage($messages); // On ajoute le message au livre d'or
                    $success = true; // On place le succès a true
                } else {
                    $errors = $messages->getErrors(); // Sinon on stock dans error notre message d'erreur
                }
            }
        }

        $messages = $livredor->getMessages();

        return $this->render('livreor/livreor.html.twig', [
            'errors' => $errors,
            'success' => $success,
            'messages' => $messages
        ]);
    }
}