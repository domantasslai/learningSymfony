<?php

namespace App\Repository;

use App\Entity\Question;
use App\Enum\AnswerStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @return Question[] Returns an array of Question objects
     */
    public function findAllAskedByNewest(): array
    {
        // Traditional way to query manyToMany relationship, when working without middle table entity
//        return $this
//            ->addIsAskedQueryBuilder()
//            ->orderBy('q.askedAt', 'DESC')
//            ->leftJoin('q.tags', 'tag')
//            ->addSelect('tag')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult();


        return $this
            ->addIsAskedQueryBuilder()
            ->orderBy('q.askedAt', 'DESC')
            ->leftJoin('q.answers', 'answer')
            ->leftJoin('q.questionTags', 'question_tag')
            ->innerJoin('question_tag.tag', 'tag')
            ->addSelect(['answer', 'question_tag', 'tag'])
            ->andWhere('answer.status = :status')
            ->setParameter('status', AnswerStatus::APPROVED->value)
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function createAskedOrderedByNewestQueryBuilder(): QueryBuilder
    {
        return $this
            ->addIsAskedQueryBuilder()
            ->orderBy('q.askedAt', 'DESC')
            ->leftJoin('q.owner', 'owner')
            ->leftJoin('q.answers', 'answer')
            ->leftJoin('q.questionTags', 'question_tag')
            ->innerJoin('question_tag.tag', 'tag')
            ->addSelect(['answer', 'question_tag', 'tag', 'owner'])
            ->andWhere('answer.status = :status')
            ->setParameter('status', AnswerStatus::APPROVED->value);
    }

    private function addIsAskedQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('q.askedAt IS NOT NULL');
    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('q');
    }

//    public function findOneBySomeField($value): ?Question
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findQuestionShowEntity($slug)
    {
        return $this
            ->addIsAskedQueryBuilder()
            ->andWhere('q.slug = :slug')
            ->setParameter('slug', $slug)
            ->leftJoin('q.owner', 'owner')
            ->leftJoin('q.answers', 'answer')
            ->addSelect(['answer', 'owner'])
            ->getQuery()
            ->getFirstResult();
    }
}
