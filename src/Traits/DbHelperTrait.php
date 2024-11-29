<?php

namespace App\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Helper trait for repositories.
 * @method createQueryBuilder(string $string)
 */
trait DbHelperTrait
{

    /**
     * Fetches a random entity from the DB or null if none exists.
     * @return mixed
     */
    public function getRandomEntity(): mixed
    {
        return $this
            ->getRandomEntityQuery()
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Returns the built (but unfinished) query to get a random entity
     * @return QueryBuilder
     */
    private function getRandomEntityQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->orderBy('RAND()')
            ->setMaxResults(1);
    }
}