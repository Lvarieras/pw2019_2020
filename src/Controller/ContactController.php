<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact/", name="app_contact")
     */

    public function contact() {
       return $this->render('contact/index.html.twig');
    }

    /**
     * @Route("/contact_form", name="app_contact_form")
     */

    public function contact_form(Request $req, MailerInterface $mailer)
    {
        $errors = null;
        $success = null;

        if($req->getMethod() == "POST") {
            $from = $req->request->get('from');
            $subject = $req->request->get('subject');
            $message = $req->request->get('message');

            if(!empty($from) && !empty($subject) && !empty($message)) {
                $this->sendEmail($from, $subject, $message, $mailer);
                $success = "Mail envoyé avec succès !";
            } else {
                $errors = array("Merci de renseigner tous les champs");
            }
        }

        return $this->render('contact_form/index.html.twig', [
            'errors' => $errors,
            'success' => $success
        ]);
    }

    public function sendEmail(String $from, String $subject, String $message, MailerInterface $mailer)
    {
        $email = (new Email())
            ->from($from)
            ->to('adeline85170@gmail.com')
            ->subject($subject)
            ->html("Envoyé par : $from <br />Message :<br />".$message);
 
        /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
        $sentEmail = $mailer->send($email);

        return new Response("Mail envoyé !");
    }
}