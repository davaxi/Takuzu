<?php

namespace Davaxi\Takuzu;

/**
 * Class Resolver_Chain
 * @package Davaxi\Takuzu
 */
class Resolver_Chain
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
     * @var Resolver_Step[]
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
        $this->setInitialStep();
    }

    /**
     * @param Resolver_Step $resolverStep
     */
    public function addResolverStep(Resolver_Step $resolverStep)
    {
        $resolverStep->setPreviousStep(
            $this->getLastResolverStep()
        );
        $resolverStep->setOriginalGrid(
            $this->getCurrentGrid()
        );
        $resolverStep->resolve();
        $this->resolverSteps[] = $resolverStep;
        $this->currentGrid = $resolverStep->getResolvedGrid();
    }

    /**
     * @return Resolver_Step
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
     * @return Resolver_Step[]
     */
    public function getResolverSteps()
    {
        return $this->resolverSteps;
    }

    protected function setInitialStep()
    {
        $resolverStep = new Resolver_Step();
        $resolverStep->setMethod(Resolver_Step::METHOD_INIT);
        $resolverStep->setOriginalGrid($this->originalGrid);
        $this->resolverSteps[] = $resolverStep;
    }
}