<?php

namespace App\Entity;

class Message
{
    //ici on créer des variables globales
    public $username;
    public $message;
    public $date;
    const LIMIT_USERNAME = 3;
    const LIMIT_MESSAGE = 10;


    public static function fromJSON(string $json)
    {
        $donnes = json_decode($json, true);
        if($donnes == null){
            return null;
        }
        return new Message($donnes['username'], $donnes['message'], new \DateTime($donnes['date']));
    }

    public function __construct(string $username, string $message, \DateTime $date = null) //notre constructeur
    {
        $this->username = $username;
        $this->message = $message;
        $this->date = $date ?: new \DateTime();
    }

    public function isValid(): bool
    {
        return empty($this->getErrors()); //notre méthode qui nous permet de savoir si un un formulaire est valide ou non
    } 

    public function getErrors(): array //notre fonction qui permet de trouver les erreurs   
    {
        $errors = []; //errors un tableau vide
        if (strlen($this->username) < self::LIMIT_USERNAME) { // Si la longueur de username est inférieur a notre limite passée en paramètre comme il s'agit d'une constante on utilise self :: 
            $errors['username'] = "Votre identifiant est trop court"; //on stock dans errors l'erreur du pseudo
        }
        if (strlen($this->message) < self::LIMIT_MESSAGE) {
            $errors['message'] = "Votre texte est trop court"; //on stock dans message l'erreur du message
        }
        return $errors;
    }
    // convertit notre message en un tableau puis sous forme de string avec json_encode

    public function toJSON(): string
    {
        return json_encode([
            'username' => $this->username, //on stock dans le username du tableau le username
            'message' => $this->message, //idem 
            'date' => $this->date->format('Y-m-d H:i:s') //idem on utilise getTimesamp pour retrouver la date
        ]);
    }
}
