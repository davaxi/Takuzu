<?php

namespace Davaxi\Takuzu;

/**
 * Class ResolverStep
 * @package Davaxi\Takuzu
 */
class ResolverStep
{
    const TYPE_LINE = 1;
    const TYPE_COLUMN = 2;

    const METHOD_INIT = 1;
    const METHOD_NO_THREE_SIDE = 2;
    const METHOD_NO_THREE_CENTER = 3;
    const METHOD_COMPLETE_EQUALITY = 4;
    const METHOD_NECESSARY = 5;
    const METHOD_NO_POSSIBLE = 6;
    const METHOD_NO_DUPLICATE = 7;
    const METHOD_TEST = 8;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var integer
     */
    protected $method;

    /**
     * @var int
     */
    protected $changedLineNo;

    /**
     * @var int
     */
    protected $changedColumnNo;

    /**
     * @var int
     */
    protected $definedValue;

    /**
     * @var array
     */
    protected $methodData = array();

    /**
     * @var Grid
     */
    protected $originalGrid;

    /**
     * @var Grid
     */
    protected $resolvedGrid;

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
     * @param ResolverStep $previousStep
     */
    public function setPreviousStep(ResolverStep $previousStep)
    {
        $this->previousStep = $previousStep;
    }

    /**
     * @param Grid $grid
     */
    public function setOriginalGrid(Grid $grid)
    {
        $this->originalGrid = clone $grid;
    }

    /**
     * @param $lineNo
     * @param $columnNo
     * @param $value
     */
    public function setGridValue($lineNo, $columnNo, $value)
    {
        $this->changedLineNo = $lineNo;
        $this->changedColumnNo = $columnNo;
        $this->definedValue = $value;
    }

    /**
     * @param $method
     */
    public function setMethod($method)
    {
        $this->checkMethod($method);
        $this->method = $method;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->checkType($type);
        $this->type = $type;
    }

    /**
     * @param array $methodData
     */
    public function setMethodData(array $methodData)
    {
        $this->methodData = $methodData;
    }

    /**
     * @return bool
     */
    public function isInitialStep()
    {
        return $this->method === static::METHOD_INIT;
    }

    /**
     * @param $method
     */
    protected function checkMethod($method)
    {
        $allowedMethods = array(
            static::METHOD_INIT,
            static::METHOD_NO_THREE_SIDE,
            static::METHOD_NO_THREE_CENTER,
            static::METHOD_COMPLETE_EQUALITY,
            static::METHOD_NECESSARY,
            static::METHOD_NO_POSSIBLE,
            static::METHOD_NO_DUPLICATE,
            static::METHOD_TEST,
        );
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException('Invalid method: ' . $method);
        }
    }

    /**
     * @param $type
     */
    protected function checkType($type)
    {
        $allowedTypes = array(
            static::TYPE_COLUMN,
            static::TYPE_LINE,
        );
        if (!in_array($type, $allowedTypes)) {
            throw new \InvalidArgumentException('Invalid type');
        }
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
    public function getResolvedGrid()
    {
        return $this->resolvedGrid;
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
        $this->resolvedGrid = $this->getOriginalGrid();
        $this->resolvedGrid->setGridValue(
            $this->changedLineNo,
            $this->changedColumnNo,
            $this->definedValue
        );
        $this->resolvedGrid->getChecker()->checkHasResolved();
    }
}