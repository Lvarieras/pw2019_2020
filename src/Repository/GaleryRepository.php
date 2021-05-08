<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class GaleryRepository extends ServiceEntityRepository {

public function __construct(ManagerRegistry $registry)
{
    parent::__construct($registry, Image::class);
}

    public function createImage(Image $image)
    {
        try {
            $this->_em->persist($image);
            $this->_em->flush();

        } catch (DBALException $e) {
                $message = sprintf('DBALException [%i]: %s', $e->getCode(), $e->getMessage());
        } catch (PDOException $e) {
                $message = sprintf('PDOException [%i]: %s', $e->getCode(), $e->getMessage());
        } catch (ORMException $e) {
                $message = sprintf('ORMException [%i]: %s', $e->getCode(), $e->getMessage());
        } catch (Exception $e) {
                $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
        }

        //return $message;
    }
}
