<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;
use App\Entity\Image;

class GaleryController extends AbstractController
{
    /**
     * @Route("/galery", name="app_galery")
     */
    public function index(Request $request)
    {
        return $this->render('galery/galery.html.twig');
    }

    /**
     * @Route("/indonesie", name="app_indonesie")
     */
    public function indonesie(Request $request)
    {
         return $this->render('galery/indonesie.html.twig');
    }

    /**
     * @Route("/malaisie", name="app_malaisie")
     */
    public function malaisie(Request $request)
    {
        return $this->render('galery/malaisie.html.twig');
    }

    /**
     * @Route("/singapore", name="app_singapore")
     */
    public function singapore(Request $request)
    {
        return $this->render('galery/singapore.html.twig');
    }

    /**
     * @Route("/thailande", name="app_thailande")
     */
    public function thailande(Request $request)
    {
        return $this->render('galery/thailande.html.twig');
    }




    /**
     * @Route("/upload", name="app_upload")
     */
     public function upload(Request $request, string $uploadDir, 
             FileUploader $uploader, LoggerInterface $logger)
    {
        $success = null;
        $errors = null;
        $image = new Image();

        if($request->getMethod() == "POST") {
            $token = $request->get("token");
            if (!$this->isCsrfTokenValid('upload', $token)) 
            {
                $logger->info("CSRF failure");
                $errors = array("Failure");
                // return new JsonResponse(["state" => "alert-danger", "content" => "Failure"]);
            }
            $image->setNom($request->request->get('selectedFile'));
            $file = $request->files->get('myfile');
            if (empty($file)) 
            {
                $errors = array("Fichier vide");
                // return new JsonResponse(["state" => "alert-danger", "content" => "Fichier vide"]);
            } 
            else 
            {
                $filename = $file->getClientOriginalName();
                $uploader->upload($uploadDir, $file, $filename);
                $imageRepository = $this->getDoctrine()->getRepository(Image::class);
                $message =  $imageRepository->createImage($image);
                if($message == "OK") {
                    $success = "Image enregistrée";
                }
                $success = "Image enregistrée";
            }
        }
        
        return $this->render('upload.html.twig', [
            'errors' => $errors,
            'success' => $success
        ]);

    }

}