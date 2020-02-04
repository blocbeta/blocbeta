<?php

namespace App\Controller;

use App\Components\Constants;
use App\Entity\Boulder;
use App\Entity\BoulderError;
use App\Service\ContextService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/error")
 */
class BoulderErrorController extends AbstractController
{
    private $entityManager;
    private $contextService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContextService $contextService
    )
    {
        $this->entityManager = $entityManager;
        $this->contextService = $contextService;
    }

    /**
     * @Route("")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted(Constants::ROLE_ADMIN);

        $errors = $this->entityManager->createQueryBuilder()
            ->select('
                partial boulderError.{id, description, createdAt, tenant}, 
                partial author.{id, username}, 
                partial boulder.{id, name, startWall},
                partial startWall.{id, name}
            ')
            ->from(BoulderError::class, 'boulderError')
            ->leftJoin('boulderError.author', 'author')
            ->leftJoin('boulderError.boulder', 'boulder')
            ->leftJoin('boulder.startWall', 'startWall')
            ->where('boulderError.tenant = :tenant')
            ->andWhere('boulderError.status = :status')
            ->setParameter('tenant', $this->contextService->getLocation()->getId())
            ->setParameter('status', BoulderError::STATUS_UNRESOLVED)
            ->getQuery()
            ->getArrayResult();

        return $this->json($errors);
    }

    /**
     * @Route("/count")
     */
    public function count()
    {
        $this->denyAccessUnlessGranted(Constants::ROLE_ADMIN);
        
        $connection = $this->entityManager->getConnection();
        $statement = 'select count(id) from boulder_error where tenant_id = :locationId and status = :status';
        $query = $connection->prepare($statement);

        $query->execute([
            'locationId' => $this->contextService->getLocation()->getId(),
            'status' => BoulderError::STATUS_UNRESOLVED
        ]);

        $results = $query->fetch();

        return $this->json($results);
    }
}