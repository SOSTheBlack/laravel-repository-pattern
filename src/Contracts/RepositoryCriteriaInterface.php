<?php

namespace SOSTheBlack\Repository\Contracts;

use Illuminate\Support\Collection;


/**
 * Interface RepositoryCriteriaInterface.
 */
interface RepositoryCriteriaInterface
{

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return $this
     */
    public function pushCriteria($criteria): static;

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria): static;

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria(): Collection;

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria): mixed;

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true): static;

    /**
     * Reset all Criterias
     *
     * @return $this
     */
    public function resetCriteria(): static;
}
