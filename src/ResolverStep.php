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
    public function getResolvedGrid()
    {
        return $this->resolverMethod->getResolvedGrid();
    }

    public function resolve()
    {
        $this->resolverMethod->compute();
        $this->getResolvedGrid()->getChecker()->checkHasResolved();
    }

}