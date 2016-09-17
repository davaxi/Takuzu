<?php

namespace Davaxi\Takuzu;

/**
 * Class Grid
 * @package Davaxi\Takuzu
 */
class Grid
{
    const UNDEFINED = -1;
    const ZERO = 0;
    const ONE = 1;

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @var array[]
     */
    protected $grid = array();

    /**
     * @var int
     */
    protected $emptyCaseCount = 0;

    /**
     * @var GridChecker
     */
    protected $checker;

    /**
     * @var GridHelpers
     */
    protected $helpers;

    /**
     * Grid constructor.
     * @param null $width
     * @param null $height
     * @throws InvalidGridException
     */
    public function __construct($width = null, $height = null)
    {
        $this->checker = new GridChecker($this);
        $this->helpers = new GridHelpers();
        if (!is_null($width) && !is_null($height)) {
            $this->checker->checkSides($width, $height);
            $this->generateEmptyGrid($width, $height);
        }
        else if (!is_null($width)) {
            throw new InvalidGridException('Not defined height');
        }
        else if (!is_null($height)) {
            throw new InvalidGridException('Not defined height');
        }
    }

    /**
     * Grid clone
     */
    public function __clone()
    {
        $this->checker = new GridChecker($this);
    }

    /**
     * @param $lineNo
     * @param $columnNo
     * @param $value
     */
    public function setGridValue($lineNo, $columnNo, $value)
    {
        $this->checker->checkGridPosition($lineNo, $columnNo);
        $this->checker->checkGridValue($value);

        $current = $this->grid[$lineNo][$columnNo];
        if ($current === $value) {
            return;
        }
        $this->grid[$lineNo][$columnNo] = $value;
        if ($current === static::UNDEFINED) {
            $this->emptyCaseCount--;
        }
        if ($value === static::UNDEFINED) {
            $this->emptyCaseCount++;
        }
    }

    /**
     * @param string $gridString
     */
    public function setGridFromString($gridString)
    {
        $lines = explode("\n", $gridString);
        $grid = array_map(
            function($line) {
                /** @var string $line */
                $rows = str_split($line, 1);
                foreach ($rows as $i => $value) {
                    if ($value === '0') {
                        $rows[$i] = static::ZERO;
                        continue;
                    }
                    if ($value === '1') {
                        $rows[$i] = static::ONE;
                        continue;
                    }
                    $rows[$i] = static::UNDEFINED;
                }
                return $rows;
            },
            $lines
        );
        $this->setGridFromArray($grid);
    }

    /**
     * @param array[] $grid
     */
    public function setGridFromArray(array $grid)
    {
        $lineCount = count($grid);
        if ($lineCount % 2 !== 0) {
            throw new \InvalidArgumentException('The grid should have an even number line.');
        }
        $emptyCaseCount = 0;
        $currentRowCount = 0;
        foreach ($grid as $line) {
            $rowCount = count($line);
            if ($rowCount % 2 !== 0) {
                throw new \InvalidArgumentException('The grid should have an even number of column.');
            }
            if (!$currentRowCount) {
                $currentRowCount = $rowCount;
            }
            else if ($currentRowCount !== $rowCount) {
                throw new \InvalidArgumentException('The grid does not form a rectangle.');
            }
            foreach ($line as $value) {
                $this->checker->checkGridValue($value);
                if ($value === static::UNDEFINED) {
                    $emptyCaseCount++;
                }
            }
        }
        $this->grid = $grid;
        $this->height = $lineCount;
        $this->width = $currentRowCount;
        $this->emptyCaseCount = $emptyCaseCount;
        $this->getChecker()->checkHasResolved();
    }

    /**
     * @return GridChecker
     */
    public function &getChecker()
    {
        return $this->checker;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return \array[]
     */
    public function getLines()
    {
        return $this->grid;
    }

    /**
     * @return \array[]
     */
    public function getGridArray()
    {
        return $this->grid;
    }

    /**
     * @return string
     */
    public function getGridString()
    {
        $result = array();
        foreach ($this->grid as $line) {
            $lineDump = array();
            foreach ($line as $value) {
                $lineDump[] = $this->helpers->getValueLabel($value);
            }
            $result[] = implode('', $lineDump);
        }
        return implode("\n", $result);
    }

    /**
     * @return \array[]
     */
    public function getUnresolvedLines()
    {
        $lines = $this->getLines();
        return array_filter(
            $lines,
            function(array $line)
            {
                return in_array(static::UNDEFINED, $line, true);
            }
        );
    }

    /**
     * @return \array[]
     */
    public function getColumns()
    {
        $columns = array_fill(0, $this->height, array());
        foreach ($this->grid as $lineNo => $line) {
            foreach ($line as $columnNo => $value) {
                $columns[$columnNo][$lineNo] = $value;
            }
        }
        return $columns;
    }

    /**
     * @return \array[]
     */
    public function getUnresolvedColumns()
    {
        $columns = $this->getColumns();
        return array_filter(
            $columns,
            function(array $column)
            {
                return in_array(static::UNDEFINED, $column, true);
            }
        );
    }

    /**
     * @return int
     */
    public function getEmptyCaseCount()
    {
        return $this->emptyCaseCount;
    }

    /**
     * @return array
     */
    public function getFirstEmptyCasePosition()
    {
        $lines = $this->getUnresolvedLines();
        if (!$lines) {
            throw new \LogicException('Not exist empty case');
        }
        $line = reset($lines);
        $lineNo = key($lines);
        $columnNo = array_search(Grid::UNDEFINED, $line, true);
        return array(
            $lineNo,
            $columnNo,
        );
    }

    /**
     * @param integer $width
     * @param integer $height
     */
    protected function generateEmptyGrid($width, $height)
    {
        $columns = array_fill(0, $width, static::UNDEFINED);
        $this->grid = array_fill(0, $height, $columns);
        $this->emptyCaseCount = $width * $height;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = array();
        foreach ($this->grid as $line) {
            $lineDump = array();
            foreach ($line as $value) {
                $lineDump[] = $this->helpers->getValueLabel($value);
            }
            $result[] = implode(' ', $lineDump);
        }
        return implode("\n", $result);
    }

}