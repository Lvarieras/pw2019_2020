<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\google\recaptcha\ReCaptcha;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/afficheUsers", name="app_affiche_users")
     */
    public function afficheUsers() {
        //On ne peut afficher les users que si le user connecté est admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On accède au repository qui est lié à la classe USER
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        //On récupère tout les users qui sont dans la BDD
        $users = $userRepository->findAll();

        //On renvoie à la vue avec le paramètre users
        return $this->render("afficheUsers.html.twig", [
            "users" => $users
        ]);
    }

    /**
     * @Route("/deleteUser", name="app_delete_user")
     */
     public function deleteUser(Request $req) {
         //On ne peut supprimer les users que si le user connecté est admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //Si on reçoit une méthode GET envoyé par le bouton du formulaire
        if($req->getMethod() == "GET") {

            //On récupère l'id du user à supprimer
            $id = $req->query->get('id');
            //On accède au repository qui est lié à la classe USER
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            //On récupère que le user qui à l'id $id
            $user = $userRepository->find($id);
            //On supprime en base le user
            $userRepository->removeUser($user);
        }
        //On renvoie rafraichie la page pour mettre à jour le tableau 
        return $this->redirectToRoute('app_affiche_users');
    }

    /**
     * @Route("/switchAdmin", name="app_switch_admin_role")
     */
     public function switchAdmin(Request $req) {
         //On ne peut switch les rôles des users que si le user connecté est admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //Si on reçoit une méthode GET envoyé par le bouton du formulaire
        if($req->getMethod() == "GET") {
            //On récupère l'id du user à supprimer
            $id = $req->query->get('id');
            //On accède au repository qui est lié à la classe USER
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            //On récupère que le user qui à l'id $id
            $user = $userRepository->find($id);

            //On récupère le  rôle du user, si il est admin, on le passe en role USER sion on le passe en admin
            if(in_array("ROLE_ADMIN",$user->getRoles())) {
                $user->setRoles([""]);
            } else {
                $user->setRoles(["ROLE_ADMIN"]);
            }
            //On remet en base le user modifié
            $userRepository->createUser($user, "");
        }
        //On renvoie rafraichie la page pour mettre à jour le tableau
        return $this->redirectToRoute('app_affiche_users');
    }

    /**
    * @Route("/profil", name="app_profil")
    */
    //Permet de mofifié les infos du client dans le profil.
    public function profil (Request $req){
        if($req->getMethod() == "POST") {
            $id = $req->request->get('id');

            if(!empty($id)) {
                $userRepository = $this->getDoctrine()->getRepository(User::class);
                $user = $userRepository->find($id);

                $user->setNom($req->request->get('nom'));
                $user->setPrenom($req->request->get('prenom'));
                $user->setMail($req->request->get('mail'));
                $user->setUsername($req->request->get('login'));
                
                $pass = $req->request->get('pass');

                //Si le client à rempli dans le profil le champs nouveau mdp , on le modifie
                if(!empty($pass)) {
                    $user->setPassword($pass);
                }
                //On verifie par une regexp le format de l'email
                if (!preg_match( " /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ ", $user->getMail())){
                    return new JsonResponse(["state" => "alert-danger", "content" => "Le mail n'est pas dans le bon format"]);
                }
                //On modifie l'user en base et on revoie une erreur sous la forme d'une Jsson Response.
                $message = $userRepository->createUser($user, $pass);
                if($message == "OK") {
                    return new JsonResponse(["state" => "alert-success", "content" => "Utilisateur modifié avec succès"]);
                } else {
                    return new JsonResponse(["state" => "alert-danger", "content" => "L'utilisateur n'a pas pu être modifé<br />$message"]);
                }
            } else {
                return new JsonResponse(["state" => "alert-danger", "content" => "Veuillez remplir tout les champs"]);
            }
        } else {
            //Si le cleint n'a pas cliqué sur modifié, on revoie à la vue sans modification du profil
            return $this->render('security/profil.html.twig', [
                "id" =>"",
                "nom" => "",
                "prenom" => "",
                "mail" => "",
                "login" => "",
                "pass" => ""
            ]);
        }
    
    }

    /**
     * @Route("/signup", name="app_signup")
     */
    // Fonction qui permet de s'inscrire sur le site
    public function signup(Request $req) {

        if($req->getMethod() == "POST") {
            $user = new User();
            $user->setNom($req->request->get('nom'));
            $user->setPrenom($req->request->get('prenom'));
            $user->setMail($req->request->get('mail'));
            $user->setUsername($req->request->get('login'));
            
            $pass = $req->request->get('pass');
            $user->setPassword($pass);
            //Création du recaptcha
            $recaptcha = new \ReCaptcha\ReCaptcha('6LfUFswUAAAAAMiVgkbnu0ocF2f6ZAFg3ed1-jmJ');
            $resp = $recaptcha->verify($req->request->get('g-recaptcha-response'));
            if (!$resp->isSuccess()) {
                $errors = $resp->getErrorCodes();
                return new JsonResponse(["state" => "alert-danger", "content" => "Captcha invalide"]);
            } else {
                if(!empty($user->getNom()) 
                && !empty($user->getPrenom())
                && !empty($user->getMail())
                && !empty($user->getUsername())
                && !empty($pass)) {
                
                    $userRepository = $this->getDoctrine()->getRepository(User::class);
                    
                    if (!preg_match( " /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ ", $user->getMail())){
                        return new JsonResponse(["state" => "alert-danger", "content" => "Le mail n'est pas dans le bon format"]);
                    }
                    
                    $message = $userRepository->createUser($user, $pass);
                    if($message == "OK") {
                        return new JsonResponse(["state" => "alert-success", "content" => "Utilisateur ajouté avec succès"]);
                    } else {
                        return new JsonResponse(["state" => "alert-danger", "content" => "L'utilisateur n'a pas pu être ajouté<br />$message"]);
                    }
                } else {
                    return new JsonResponse(["state" => "alert-danger", "content" => "Veuillez remplir tout les champs"]);
                }
            }            
        } else {
            return $this->render('security/signup.html.twig', [
                "nom" => "",
                "prenom" => "",
                "mail" => "",
                "login" => "",
                "pass" => ""
            ]);
        }
    }
}
