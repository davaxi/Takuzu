<?php

namespace Davaxi\Takuzu;
use Davaxi\Takuzu\ResolverMethod\RandomValue;

/**
 * Class Resolver
 * @package Davaxi\Takuzu
 */
class Resolver
{
    /**
     * @var bool
     */
    protected $resolved = false;

    /**
     * @var Grid
     */
    protected $originalGrid;

    /**
     * @var Grid
     */
    protected $resolvedGrid;

    /**
     * @var ResolverChain[]
     */
    protected $chains = array();

    /**
     * @var ResolverMethod[]
     */
    protected $resolverMethods = array(
        '\Davaxi\Takuzu\ResolverMethod\NoThreeSide',
        '\Davaxi\Takuzu\ResolverMethod\NoThreeCenter',
        '\Davaxi\Takuzu\ResolverMethod\CompleteEquality',
        '\Davaxi\Takuzu\ResolverMethod\NecessaryValues',
        '\Davaxi\Takuzu\ResolverMethod\NoPossibleRange',
    );

    /**
     * Resolver constructor.
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->originalGrid = $grid;
        $this->chains[] = new ResolverChain($grid);
        $this->resolved = $this->originalGrid->getChecker()->hasResolved();
        if ($this->resolved) {
            $this->resolvedGrid = $grid;
        }
    }

    /**
     * @return bool
     */
    public function hasResolved()
    {
        return $this->resolved;
    }

    /**
     * @return Grid
     */
    public function getResolvedGrid()
    {
        return $this->resolvedGrid;
    }

    /**
     * @param Grid $grid
     * @return ResolverMethod|null
     */
    protected function foundNextResolveGridMethod(Grid $grid)
    {
        foreach ($this->resolverMethods as $resolverMethodClass) {
            /** @var ResolverMethod $resolverMethod */
            $resolverMethod = new $resolverMethodClass($grid);
            if ($resolverMethod->found()) {
                return $resolverMethod;
            }
        }
        return null;
    }

    /**
     * @param Grid $grid
     * @param $value
     * @return ResolverStep
     */
    protected function generateTryResolverStep(Grid $grid, $value)
    {
        list($lineNo, $columnNo) = $grid->getFirstEmptyCasePosition();
        $resolverMethod = new RandomValue($grid);
        $resolverMethod->setValue($lineNo, $columnNo, $value);
        return $this->generateResolverStep($resolverMethod);
    }

    /**
     * @param ResolverMethod $resolverMethod
     * @return ResolverStep
     */
    protected function generateResolverStep(ResolverMethod $resolverMethod)
    {
        $nextResolveStep = new ResolverStep();
        $nextResolveStep->setResolverMethod($resolverMethod);
        $nextResolveStep->resolve();
        return $nextResolveStep;
    }

    protected function nextResolveSteps()
    {
        if ($this->resolved) {
            throw new InvalidGridException('Grid already resolved');
        }
        $chains = array();
        foreach ($this->chains as $resolverChain) {
            $grid = $resolverChain->getCurrentGrid();
            try {
                $resolveMethod = $this->foundNextResolveGridMethod($grid);
            }
            catch (InvalidGridException $e) {
                continue;
            }

            if ($resolveMethod === null) {
                if (!$grid->getEmptyCaseCount()) {
                    continue;
                }

                $secondResolverChain = clone $resolverChain;

                $nextResolverStep = $this->generateTryResolverStep($grid, Grid::ZERO);
                $resolverChain->addResolverStep($nextResolverStep);
                $chains[] = $resolverChain;

                $nextResolverStep = $this->generateTryResolverStep($grid, Grid::ONE);
                $secondResolverChain->addResolverStep($nextResolverStep);
                $chains[] = $secondResolverChain;
                continue;
            }

            $nextResolverStep = $this->generateResolverStep($resolveMethod);
            $resolverChain->addResolverStep($nextResolverStep);
            $chains[] = $resolverChain;

            $resolvedGrid = $nextResolverStep->getResolvedGrid();
            if ($grid->getGridString() === $resolvedGrid->getGridString()) {
                throw new \LogicException('No change ?');
            }
            if ($resolvedGrid->getChecker()->hasResolved()) {
                $this->resolved = true;
                $this->resolvedGrid = $resolvedGrid;
            }
        }
        $this->chains = $chains;
    }

    public function resolve()
    {
        while (true) {
            $this->nextResolveSteps();
            if ($this->resolved) {
                break;
            }
            if (!$this->chains) {
                throw new \UnexpectedValueException('Not found next resolved method');
            }

        }
    }
}