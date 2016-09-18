<?php

namespace Davaxi\Takuzu;

/**
 * Class ResolverMethod
 * @package Davaxi\Takuzu
 */
abstract class ResolverMethod
{
    const TYPE_LINE = 0;
    const TYPE_COLUMN = 1;

    const METHOD_NAME = '';

    /**
     * @var array
     */
    protected static $values = array(
        Grid::ZERO,
        Grid::ONE
    );

    /**
     * @var Grid
     */
    protected $originalGrid;

    /**
     * @var Grid
     */
    protected $resolvedGrid;

    /**
     * @var integer
     */
    protected $foundedLineType;

    /**
     * @var integer
     */
    protected $foundedLineNo;

    /**
     * @var array
     */
    protected $foundedValues = array();

    /**
     * @var bool
     */
    protected $founded = false;

    /**
     * ResolverMethod constructor.
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->originalGrid = $grid;
    }

    /**
     * @return Grid
     */
    public function &getOriginalGrid()
    {
        return $this->originalGrid;
    }

    /**
     * @return Grid
     */
    public function &getResolvedGrid()
    {
        return $this->resolvedGrid;
    }

    /**
     * @return bool
     */
    public function found()
    {
        $lines = $this->originalGrid->getUnresolvedLines();
        $found = $this->foundOnGridLines(static::TYPE_LINE, $lines);
        if ($found) {
            return true;
        }
        $columns = $this->originalGrid->getUnresolvedColumns();
        return $this->foundOnGridLines(static::TYPE_COLUMN, $columns);
    }

    public function compute()
    {
        $grid = clone $this->originalGrid;
        if (!$this->foundedValues) {
            throw new InvalidGridException('Invalid process');
        }
        foreach ($this->foundedValues as $position => $value) {
            if ($this->foundedLineType == static::TYPE_LINE) {
                $grid->setGridValue(
                    $this->foundedLineNo,
                    $position,
                    $value
                );
                continue;
            }
            $grid->setGridValue(
                $position,
                $this->foundedLineNo,
                $value
            );
        }
        $this->resolvedGrid = $grid;
    }

    /**
     * @param $lineType
     * @param array $lines
     * @return bool
     */
    protected function foundOnGridLines($lineType, array $lines)
    {
        foreach ($lines as $lineNo => $line) {
            $found = $this->foundOnGridLine($line);
            if ($found) {
                $this->foundedLineType = $lineType;
                $this->foundedLineNo = $lineNo;
                $this->founded = true;
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $line
     * @return boolean
     */
    abstract protected function foundOnGridLine(array $line);

}