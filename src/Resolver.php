<?php

namespace Davaxi\Takuzu;

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
     * @var Resolver_Chain[]
     */
    protected $chains = array();

    /**
     * Resolver constructor.
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->originalGrid = $grid;
        $this->chains[] = new Resolver_Chain($grid);
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
     * @return array
     */
    protected function foundNextResolveGridMethod(Grid $grid)
    {
        $found = $this->foundNoThreeSideGrid($grid);
        if ($found) {
            return $found;
        }
        $found = $this->foundNoThreeCenterGrid($grid);
        if ($found) {
            return $found;
        }
        $found = $this->foundCompleteEqualityGrid($grid);
        if ($found) {
            return $found;
        }
        $found = $this->foundNecessaryGrid($grid);
        if ($found) {
            return $found;
        }
        $found = $this->foundNoPossibleRangeGrid($grid);
        if ($found) {
            return $found;
        }
        return array();
    }

    /**
     * @param Grid $grid
     * @return array
     */
    protected function foundNoThreeSideGrid(Grid $grid)
    {
        return $this->foundMethodOnGrid(
            $grid,
            Resolver_Step::METHOD_NO_THREE_SIDE,
            'foundNoThreeSideGridLine'
        );
    }

    /**
     * @param Grid $grid
     * @return array
     */
    protected function foundNoThreeCenterGrid(Grid $grid)
    {
        return $this->foundMethodOnGrid(
            $grid,
            Resolver_Step::METHOD_NO_THREE_CENTER,
            'foundNoThreeCenterGridLine'
        );
    }

    /**
     * @param Grid $grid
     * @return array
     */
    protected function foundCompleteEqualityGrid(Grid $grid)
    {
        return $this->foundMethodOnGrid(
            $grid,
            Resolver_Step::METHOD_COMPLETE_EQUALITY,
            'foundCompleteEqualityGridLine'
        );
    }

    /**
     * @param Grid $grid
     * @return array
     */
    protected function foundNecessaryGrid(Grid $grid)
    {
        return $this->foundMethodOnGrid(
            $grid,
            Resolver_Step::METHOD_NECESSARY,
            'foundNecessaryGridLine'
        );
    }

    /**
     * @param Grid $grid
     * @return array
     */
    protected function foundNoPossibleRangeGrid(Grid $grid)
    {
        return $this->foundMethodOnGrid(
            $grid,
            Resolver_Step::METHOD_NO_POSSIBLE,
            'foundNoPossibleRangeGridLine'
        );
    }

    /**
     * @param array $line
     * @return array
     */
    protected function foundNoThreeSideGridLine(array $line)
    {
        $length = count($line);
        foreach ($line as $lineNo => $value) {
            if ($value !== Grid::UNDEFINED) {
                continue;
            }
            if ($lineNo >= Checker::CONSECUTIVE_LIMIT) {
                $values = array_slice(
                    $line,
                    $lineNo - Checker::CONSECUTIVE_LIMIT,
                    Checker::CONSECUTIVE_LIMIT
                );
                if ($this->checkDoubleValue($values)) {
                    $reverseValue = Grid::getReverseValue(
                        $values[0]
                    );
                    return array(
                        $lineNo,
                        $reverseValue,
                        range(
                            $lineNo - Checker::CONSECUTIVE_LIMIT,
                            $lineNo - 1,
                            1
                        ),
                    );
                }
            }
            if ($lineNo < ($length - Checker::CONSECUTIVE_LIMIT)) {
                $values = array_slice(
                    $line,
                    $lineNo + 1,
                    Checker::CONSECUTIVE_LIMIT
                );
                if ($this->checkDoubleValue($values)) {
                    $reverseValue = Grid::getReverseValue(
                        $values[0]
                    );
                    return array(
                        $lineNo,
                        $reverseValue,
                        range(
                            $lineNo + 1,
                            $lineNo + Checker::CONSECUTIVE_LIMIT,
                            1
                        )
                    );
                }
            }
        }
        return array();
    }

    /**
     * @param array $line
     * @return array
     */
    protected function foundNoThreeCenterGridLine(array $line)
    {
        $length = count($line);
        foreach ($line as $lineNo => $value) {
            if ($lineNo === 0) {
                continue;
            }
            if ($lineNo === ($length - 1)) {
                continue;
            }
            if ($value !== Grid::UNDEFINED) {
                continue;
            }
            if ($line[$lineNo - 1] === Grid::UNDEFINED) {
                continue;
            }
            if ($line[$lineNo - 1] !== $line[$lineNo + 1]) {
                continue;
            }
            $reverseValue = Grid::getReverseValue($line[$lineNo - 1]);
            return array(
                $lineNo,
                $reverseValue,
                array(
                    $lineNo - 1,
                    $lineNo + 1,
                ),
            );
        }
        return array();
    }

    /**
     * @param array $line
     * @return array
     */
    protected function foundCompleteEqualityGridLine(array $line)
    {
        $length = count($line);
        $undefinedLineNo = null;
        $values = array(
            Grid::ONE => 0,
            Grid::ZERO => 0,
        );
        foreach ($line as $lineNo => $value) {
            if ($value === Grid::UNDEFINED) {
                if ($undefinedLineNo === null) {
                    $undefinedLineNo = $lineNo;
                }
                continue;
            }
            $values[$value]++;
        }
        $expectedCountValue = $length / 2;
        foreach ($values as $value => $count) {
            if ($count !== $expectedCountValue) {
                continue;
            }
            $reverseValue = Grid::getReverseValue($value);
            return array(
                $undefinedLineNo,
                $reverseValue,
                array(),
            );
        }
        return array();
    }

    /**
     * @param array $line
     * @return array
     */
    protected function foundNecessaryGridLine(array $line)
    {
        $count = count($line);
        $needValues = Grid::getMissingLineValueDistribution($line);
        $undefinedRanges = Grid::getUndefinedRangeLine($line);
        foreach ($undefinedRanges as $i => &$range) {
            $range['needs'] = array(
                Grid::ONE => 0,
                Grid::ZERO => 0,
            );
            $rangeLength = $range['max'] - $range['min'] + 1;
            if ($rangeLength < 2) {
                unset($range);
                continue;
            }
            if ($range['min'] > 0) {
                $leftRangeValue = $line[$range['min'] - 1];
                $range['needs'][$leftRangeValue] = max(
                    $range['needs'][$leftRangeValue],
                    $this->getNoPossibleGridLineRangeValueMin($rangeLength)
                );
                $reverseLeftRangeValue = Grid::getReverseValue($leftRangeValue);
                $range['needs'][$reverseLeftRangeValue] = max(
                    $range['needs'][$reverseLeftRangeValue],
                    $this->getNoPossibleGridLineRangeReverseValueMin($rangeLength)
                );
            }
            if ($range['max'] < ($count - 1)) {
                $rightRangeValue = $line[$range['max'] + 1];
                $range['needs'][$rightRangeValue] = max(
                    $range['needs'][$rightRangeValue],
                    $this->getNoPossibleGridLineRangeValueMin($rangeLength)
                );
                $reverseRightRangeValue = Grid::getReverseValue($rightRangeValue);
                $range['needs'][$reverseRightRangeValue] = max(
                    $range['needs'][$reverseRightRangeValue],
                    $this->getNoPossibleGridLineRangeReverseValueMin($rangeLength)
                );
            }
            $needValues[Grid::ZERO] -= $range['needs'][Grid::ZERO];
            $needValues[Grid::ONE] -= $range['needs'][Grid::ONE];
            unset($range);
        }

        if ($needValues[Grid::ZERO] < 0 || $needValues[Grid::ONE] < 0) {
            throw new InvalidGridException('Grid line not to be resolved.');
        }

        if ($needValues[Grid::ZERO] === 0) {
            foreach ($undefinedRanges as $i => $range) {
                if ($range['needs'][Grid::ZERO] !== 0) {
                    continue;
                }
                return array(
                    $range['min'],
                    Grid::ONE,
                    $undefinedRanges
                );
            }
        }
        if ($needValues[Grid::ONE] === 0) {
            foreach ($undefinedRanges as $i => $range) {
                if ($range['needs'][Grid::ONE] !== 0) {
                    continue;
                }
                return array(
                    $range['min'],
                    Grid::ZERO,
                    $undefinedRanges
                );
            }
        }
        return array();
    }

    /**
     * @param array $line
     * @return array
     */
    protected function foundNoPossibleRangeGridLine(array $line)
    {
        $undefinedRanges = Grid::getUndefinedRangeLine($line);
        if (count($undefinedRanges) !== 1) {
            return array();
        }
        $count = count($line);
        $undefinedRange = $undefinedRanges[0];

        $leftRangeValues = array();
        if ($undefinedRange['min'] > 0) {
            array_unshift($leftRangeValues, $line[$undefinedRange['min'] - 1]);
            if ($undefinedRange['min'] > 1) {
                array_unshift($leftRangeValues, $line[$undefinedRange['min'] - 2]);
            }
        }
        $rightRangeValues = array();
        if ($undefinedRange['max'] < ($count - 1)) {
            $rightRangeValues[] = $line[$undefinedRange['max'] + 1];
            if (($undefinedRange['max'] + 1) < ($count - 1)) {
                $rightRangeValues[] = $line[$undefinedRange['max'] + 2];
            }
        }
        $needValues = Grid::getMissingLineValueDistribution($line);
        $possibilities = Grid::getRangePossibilities(
            $needValues,
            $leftRangeValues,
            $rightRangeValues
        );
        if (count($possibilities) < 1) {
            throw new InvalidGridException('Grid not solvable');
        }
        $positions = array_fill(0, $undefinedRange['max'] - $undefinedRange['min'] + 1, null);
        foreach ($possibilities as $possibility) {
            foreach ($positions as $positionNo => $currentPositionValue) {
                if ($currentPositionValue === null) {
                    $positions[$positionNo] = $possibility[$positionNo];
                    continue;
                }
                if ($currentPositionValue !== $possibility[$positionNo]) {
                    unset($positions[$positionNo]);
                    continue;
                }
            }
        }
        if (!$positions) {
            return array();
        }
        $positionValue = reset($positions);
        $positionKey = key($positions);
        return array(
            $undefinedRange['min'] + $positionKey,
            $positionValue,
            array(
                'range' => $undefinedRange,
                'possibilities' => $possibilities,
            ),
        );

    }

    /**
     * @param $rangeLength
     * @return int
     */
    protected function getNoPossibleGridLineRangeValueMin($rangeLength)
    {
        if ($rangeLength < 2) {
            return 0;
        }
        return (int)floor($rangeLength / 3);
    }

    /**
     * @param $rangeLength
     * @return int
     */
    protected function getNoPossibleGridLineRangeReverseValueMin($rangeLength)
    {
        if ($rangeLength < 2) {
            return 0;
        }
        return (int)floor(($rangeLength + 1) / 3);
    }

    /**
     * @param Grid $grid
     * @param $method
     * @param $callback
     * @return array
     */
    protected function foundMethodOnGrid(Grid $grid, $method, $callback)
    {
        $unresolvedLines = $grid->getUnresolvedLines();
        foreach ($unresolvedLines as $lineNo => $line) {
            $found = $this->$callback($line);
            if (!$found) {
                continue;
            }
            list($columnNo, $value, $methodData) = $found;
            return array(
                $method,
                Resolver_Step::TYPE_LINE,
                $lineNo,
                $columnNo,
                $value,
                $methodData
            );
        }
        $unresolvedColumns = $grid->getUnresolvedColumns();
        foreach ($unresolvedColumns as $columnNo => $column) {
            $found = $this->$callback($column);
            if (!$found) {
                continue;
            }
            list($lineNo, $value, $methodData) = $found;
            return array(
                $method,
                Resolver_Step::TYPE_COLUMN,
                $lineNo,
                $columnNo,
                $value,
                $methodData
            );
        }
        return array();
    }

    /**
     * @param array $values
     * @return bool
     */
    protected function checkDoubleValue(array $values)
    {
        if (in_array(Grid::UNDEFINED, $values, true)) {
            return false;
        }
        $values = array_unique($values);
        return count($values) === 1;
    }

    /**
     * @param Resolver_Step $previousStep
     * @param Grid $grid
     * @param $value
     * @return Resolver_Step
     */
    protected function generateTryResolverStep(Resolver_Step $previousStep, Grid $grid, $value)
    {
        list($lineNo, $columnNo) = $grid->getFirstEmptyCasePosition();
        return $this->generateResolverStep(
            $previousStep,
            $grid,
            array(
                Resolver_Step::METHOD_TEST,
                Resolver_Step::TYPE_LINE,
                $lineNo,
                $columnNo,
                $value,
                array(),
            )
        );
    }

    /**
     * @param Resolver_Step $previousStep
     * @param Grid $grid
     * @param array $resolveData
     * @return Resolver_Step
     */
    protected function generateResolverStep(Resolver_Step $previousStep, Grid $grid, array $resolveData)
    {
        list($method, $type, $lineNo, $columnNo, $value, $methodData) = $resolveData;

        $nextResolveStep = new Resolver_Step();
        $nextResolveStep->setType($type);
        $nextResolveStep->setMethod($method);
        $nextResolveStep->setMethodData($methodData);
        $nextResolveStep->setPreviousStep($previousStep);
        $nextResolveStep->setOriginalGrid($grid);
        $nextResolveStep->setGridValue($lineNo, $columnNo, $value);
        $nextResolveStep->resolve();
        return $nextResolveStep;
    }

    protected function nextResolveSteps()
    {
        if ($this->resolved) {
            throw new InvalidGridException('Grid already resolved');
        }

        $chains = array();
        foreach ($this->chains as $i => $resolverChain) {
            $grid = $resolverChain->getCurrentGrid();
            $lastResolverStep = $resolverChain->getLastResolverStep();
            try {
                $nextResolverData = $this->foundNextResolveGridMethod($grid);
            }
            catch (InvalidGridException $e) {
                continue;
            }

            if (!$nextResolverData) {
                if ($grid->getEmptyCaseCount()) {
                    $secondResolverChain = clone $resolverChain;

                    $nextResolverStep = $this->generateTryResolverStep($lastResolverStep, $grid, Grid::ZERO);
                    $resolverChain->addResolverStep($nextResolverStep);
                    $chains[] = $resolverChain;

                    $nextResolverStep = $this->generateTryResolverStep($lastResolverStep, $grid, Grid::ONE);
                    $secondResolverChain->addResolverStep($nextResolverStep);
                    $chains[] = $secondResolverChain;
                }
                continue;
            }

            $nextResolverStep = $this->generateResolverStep(
                $lastResolverStep,
                $grid,
                $nextResolverData
            );
            $resolverChain->addResolverStep($nextResolverStep);
            $chains[] = $resolverChain;

            $resolvedGrid = $nextResolverStep->getResolvedGrid();

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