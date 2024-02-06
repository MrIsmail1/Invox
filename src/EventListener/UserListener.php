<?php

namespace App\EventListener;

use App\Entity\CompanyDetails;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\prePersistEventArgs;
use Doctrine\ORM\Events;


#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
class UserListener
{
    public function prePersist(User $user, prePersistEventArgs $event)
    {
        $objectManager = $event->getObjectManager();
        if ($user->getRoles() === ['ROLE_USER']) {
            $companyDetails = new CompanyDetails();
            $companyDetails->setCompanyEmail($user->getEmail());
            $user->setCompanyDetails($companyDetails);
            $objectManager->persist($companyDetails);
            $objectManager->flush();
        }
    }
}