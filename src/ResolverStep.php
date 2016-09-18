<?php

namespace Davaxi\Takuzu;

/**
 * Class ResolverStep
 * @package Davaxi\Takuzu
 */
class ResolverStep
{
    /**
     * @var ResolverMethod
     */
    protected $resolverMethod;

    /**
     * @var ResolverStep
     */
    protected $previousStep;

    /**
     * @return bool
     */
    public function hasPreviousStep()
    {
        return $this->previousStep instanceof ResolverStep;
    }

    /**
     * @param ResolverMethod $resolverMethod
     */
    public function setResolverMethod(ResolverMethod $resolverMethod)
    {
        $this->resolverMethod = &$resolverMethod;
    }

    /**
     * @param ResolverStep $previousStep
     */
    public function setPreviousStep(ResolverStep $previousStep)
    {
        $this->previousStep = $previousStep;
    }

    /**
     * @return Grid
     */
    public function getOriginalGrid()
    {
        return $this->resolverMethod->getOriginalGrid();
    }

    /**
     * @return Grid
     */
    public function getResolvedGrid()
    {
        return $this->resolverMethod->getResolvedGrid();
    }

    /**
     * @return ResolverStep
     */
    public function getPreviousStep()
    {
        return $this->previousStep;
    }

    public function resolve()
    {
        $this->resolverMethod->compute();
        $this->getResolvedGrid()->getChecker()->checkHasResolved();
    }

}