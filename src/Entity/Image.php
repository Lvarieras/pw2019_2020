<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GaleryRepository")
 * @ORM\Table(name="galery")
 */

 class Image {

      /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=false)
     */
    private $nom;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Nom
     */
    public function getNom(): string
    {
        return (string) $this->nom;
    }

    public function setNom(string $nom): string
    {
        $this->nom = $nom;
        
        return $this->nom;
    }

 }

 