<?php

namespace Davaxi\Takuzu;

/**
 * Class Checker
 * @package Davaxi\Takuzu
 */
class Checker
{
    const CONSECUTIVE_LIMIT = 2;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var bool
     */
    protected $resolved = false;

    /**
     * Checker constructor.
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = &$grid;
    }

    /**
     * Compute resolved flag
     */
    public function checkHasResolved()
    {
        $this->resolved = false;
        if ($this->grid->getEmptyCaseCount()) {
            return;
        }
        $lines = $this->grid->getLines();
        if (!$this->checkLines($lines)) {
            return;
        }
        $columns = $this->grid->getColumns();
        if (!$this->checkLines($columns)) {
            return;
        }
        $this->resolved = true;
        return;
    }

    /**
     * @param integer $width
     * @param integer $height
     */
    public function checkSides($width, $height)
    {
        $this->checkSideLength('width', $width);
        $this->checkSideLength('height', $height);
    }

    /**
     * @param integer $lineNo
     * @param integer $columnNo
     * @throws InvalidGridException
     */
    public function checkGridPosition($lineNo, $columnNo)
    {
        if ($lineNo < 0 || $lineNo >= $this->grid->getHeight()) {
            throw new InvalidGridException('Expected line: 0 to ' . $this->grid->getHeight());
        }
        if ($columnNo < 0 || $columnNo >= $this->grid->getWidth()) {
            throw new InvalidGridException('Expected column: 0 to ' . $this->grid->getWidth());
        }
    }

    /**
     * @param $value
     */
    public function checkGridValue($value)
    {
        $allowedValues = array(
            Grid::UNDEFINED,
            Grid::ZERO,
            Grid::ONE,
        );
        if (!in_array($value, $allowedValues, true)) {
            throw new \InvalidArgumentException('Expected value: ' . implode(', ', $allowedValues));
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
     * @param array $lines
     * @return bool
     */
    protected function checkLines(array &$lines)
    {
        foreach ($lines as $line) {
            if (!$this->checkLineValuesDefined($line)) {
                return false;
            }
            if (!$this->checkLineValuesCollision($line)) {
                return false;
            }
            if (!$this->checkLineValuesEqualities($line)) {
                return false;
            }
        }
        if (!$this->checkLinesDistinctSeries($lines)) {
            return false;
        }
        return true;
    }

    /**
     * @param array $line
     * @return bool
     */
    protected function checkLineValuesDefined(array $line)
    {
        foreach ($line as $value) {
            if ($value === Grid::UNDEFINED) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $line
     * @return boolean
     */
    protected function checkLineValuesCollision(array $line)
    {
        $previousValue = null;
        $previousValueCount = 1;
        foreach ($line as $value) {
            if ($previousValue !== $value) {
                $previousValue = $value;
                $previousValueCount = 1;
                continue;
            }
            $previousValueCount++;
            if ($previousValueCount > static::CONSECUTIVE_LIMIT) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $line
     * @return bool
     */
    protected function checkLineValuesEqualities(array $line)
    {
        $equality = 0;
        foreach ($line as $value) {
            if ($value === Grid::ZERO) {
                $equality++;
                continue;
            }
            if ($value === Grid::ONE) {
                $equality--;
                continue;
            }
        }
        return $equality === 0;
    }

    /**
     * @param array $lines
     * @return bool
     */
    protected function checkLinesDistinctSeries(array $lines)
    {
        $convertedLines = array_map(function(array $line) {
            $line = array_map('\Davaxi\Takuzu\Grid::getValueLabel', $line);
            return implode($line);
        }, $lines);
        return array_unique($convertedLines) === $convertedLines;
    }

    /**
     * @param string $side
     * @param integer $length
     * @throws InvalidGridException
     */
    protected function checkSideLength($side, $length)
    {
        if (!is_numeric($length)) {
            throw new InvalidGridException('Invalid ' . $side . ' format');
        }
        $length = (int)$length;
        if ($length <= 0) {
            throw new InvalidGridException('Invalid ' . $side . ' value');
        }
        if ($length % 2 !== 0) {
            throw new InvalidGridException('Invalid ' . $side . ' value');
        }
    }
}