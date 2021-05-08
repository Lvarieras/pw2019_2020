<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Exception\YourBadRequestException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * @nd($id, $lockMode = null, $lockVersion = null)
 * @memethod User|null fithod User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $passwordEncoder;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Used to insert a new user.
     */
    public function createUser(UserInterface $user, string $newPassword): string
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        if($newPassword != null){
                $user->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $newPassword
                ));  
        }

        try {
            $this->_em->persist($user);
            $this->_em->flush();

            $message = "OK";
        } catch(UniqueConstraintViolationException $e){
                $message = "L'email a déjà été utilisé";
        } catch (DBALException $e) {
                $message = sprintf('DBALException [%i]: %s', $e->getCode(), $e->getMessage());
        } catch (PDOException $e) {
                $message = sprintf('PDOException [%i]: %s', $e->getCode(), $e->getMessage());
        } catch (ORMException $e) {
                $message = sprintf('ORMException [%i]: %s', $e->getCode(), $e->getMessage());
        } catch (Exception $e) {
                $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
        }

        return $message;
    }

    /**
     * Used to delete a user.
     */
     public function removeUser(UserInterface $user): string
     {
         if (!$user instanceof User) {
             throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
         }
 
         try {
             $this->_em->remove($user);
             $this->_em->flush();
 
             $message = "OK";
         } catch (DBALException $e) {
                 $message = sprintf('DBALException [%i]: %s', $e->getCode(), $e->getMessage());
         } catch (PDOException $e) {
                 $message = sprintf('PDOException [%i]: %s', $e->getCode(), $e->getMessage());
         } catch (ORMException $e) {
                 $message = sprintf('ORMException [%i]: %s', $e->getCode(), $e->getMessage());
         } catch (Exception $e) {
                 $message = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
         }
 
         return $message;
     }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $newPassword
        ));
        $this->_em->persist($user);
        $this->_em->flush();
    }

}
