<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Enum\AnswerStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Answer>
 *
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public static function createApprovedCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(
                Criteria::expr()
                    ->eq(
                        'status',
                        AnswerStatus::APPROVED
                    )
            );
    }

    /**
     * @param int $max
     * @return Answer[]
     */
    public function findAllApproved(int $max = 10): array
    {
        return $this->createQueryBuilder('answer')
            ->addCriteria(self::createApprovedCriteria())
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Answer[]
     */
    public function findMostPopular(?string $search = null): array
    {
        $query = $this->createQueryBuilder('answer')
            ->addCriteria(self::createApprovedCriteria())
            ->orderBy('answer.votes', 'DESC')
            ->innerJoin('answer.question', 'question')
            ->addSelect('question')
            ->setMaxResults(10);

        if (!empty($search)) {
            $query->andWhere('answer.content LIKE :search OR question.question LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}
