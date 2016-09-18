<?php

namespace Davaxi\Takuzu;

/**
 * Class ResolverChain
 * @package Davaxi\Takuzu
 */
class ResolverChain
{
    /**
     * @var Grid
     */
    protected $originalGrid;

    /**
     * @var Grid
     */
    protected $currentGrid;

    /**
     * @var ResolverStep[]
     */
    protected $resolverSteps = array();

    /**
     * Resolver_Chain constructor.
     * @param Grid $originalGrid
     */
    public function __construct(Grid $originalGrid)
    {
        $this->originalGrid = $originalGrid;
        $this->currentGrid = $originalGrid;
    }

    /**
     * @param ResolverStep $resolverStep
     */
    public function addResolverStep(ResolverStep $resolverStep)
    {
        if ($this->resolverSteps) {
            $resolverStep->setPreviousStep(
                $this->getLastResolverStep()
            );
        }
        $this->resolverSteps[] = $resolverStep;
        $this->currentGrid = $resolverStep->getResolvedGrid();
    }

    /**
     * @return mixed
     */
    public function getLastResolverStep()
    {
        return end($this->resolverSteps);
    }

    /**
     * @return Grid
     */
    public function getOriginalGrid()
    {
        return $this->originalGrid;
    }

    /**
     * @return Grid
     */
    public function getCurrentGrid()
    {
        return $this->currentGrid;
    }

    /**
     * @return ResolverStep[]
     */
    public function getResolverSteps()
    {
        return $this->resolverSteps;
    }

}