<?php

namespace App\Entity;

use App\Entity\Message;

class Livredor
{
    private $fichier; //on prends une variable privée fichier étant notre lien

    public function __construct(string $fichier) //on fait notre constructeur
    {
        $directory = dirname($fichier); // on récupère le chemin de fichier
        if (!is_dir($directory)) { //si le $fichier n'est pas un dossier
            mkdir($directory, 0777, true); //on le créer
        }
        if (!file_exists($fichier)) { //vérifier si le $fichier 
            touch($fichier); //modifie la date de modification
        }
        $this->fichier = $fichier; //on le stock dans le fichier
    }

    public function addMessage(message $message): void //rajoute un fichier a notre fichier en utilisant la methode toJSON
    {
        file_put_contents($this->fichier, $message->toJSON() . "\n", FILE_APPEND);
    }

    // fonction pour recupérer le message
    public function getMessages(): array
    {
        $contenus = trim(file_get_contents($this->fichier)); //supprimer les espaces en convertissant le fichier en string
        $lignes = explode("\n", $contenus); // scinde le string à chaque fin de ligne
        $messages = []; //initialise le message à tableau vide
        foreach ($lignes as $ligne) {
           $messages[] = Message::fromJSON($ligne);
        }
        return array_reverse($messages); //on renvoie le tableau de tous les messages dans le sens inverse pour avoir les message dans l'odre décroissant
    }
}
