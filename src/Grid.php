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

    const CONSECUTIVE_LIMIT = 2;

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
     * @var bool
     */
    protected $resolved = false;

    /**
     * @var int
     */
    protected $emptyCaseCount = 0;

    /**
     * Grid constructor.
     * @param null $width
     * @param null $height
     */
    public function __construct($width = null, $height = null)
    {
        if (!is_null($width) && !is_null($height)) {
            $this->checkSideLength('width', $width);
            $this->checkSideLength('height', $height);

            $this->width = (int)$width;
            $this->height = (int)$height;
            $this->generateEmptyGrid();
        }
        else if (!is_null($width)) {
            throw new \InvalidArgumentException('Not defined height');
        }
        else if (!is_null($height)) {
            throw new \InvalidArgumentException('Not defined height');
        }
    }

    /**
     * @param $lineNo
     * @param $columnNo
     * @param $value
     */
    public function setGridValue($lineNo, $columnNo, $value)
    {
        $this->checkGridPosition($lineNo, $columnNo);
        $this->checkGridValue($value);

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
     * @param $value
     */
    protected function checkGridValue($value)
    {
        $allowedValues = array(
            static::UNDEFINED,
            static::ZERO,
            static::ONE,
        );
        if (!in_array($value, $allowedValues, true)) {
            throw new \InvalidArgumentException('Expected value: ' . implode(', ', $allowedValues));
        }
    }

    /**
     * @param $lineNo
     * @param $columnNo
     */
    protected function checkGridPosition($lineNo, $columnNo)
    {
        if ($lineNo < 0 || $lineNo >= $this->height) {
            throw new \InvalidArgumentException('Expected line: 0 to ' . $this->height);
        }
        if ($columnNo < 0 || $columnNo >= $this->width) {
            throw new \InvalidArgumentException('Expected column: 0 to ' . $this->width);
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
                foreach ($rows as $i => &$row) {
                    if ($row === '0') {
                        $row = static::ZERO;
                    }
                    else if ($row === '1') {
                        $row = static::ONE;
                    }
                    else {
                        $row = static::UNDEFINED;
                    }
                    unset($row);
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
        foreach ($grid as $i => $line) {
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
            foreach ($line as $j => $value) {
                $this->checkGridValue($value);
                if ($value === static::UNDEFINED) {
                    $emptyCaseCount++;
                }
            }
        }
        $this->grid = $grid;
        $this->height = $lineCount;
        $this->width = $currentRowCount;
        $this->emptyCaseCount = $emptyCaseCount;
        $this->checkHasResolved();
    }

    /**
     * @return bool
     */
    public function hasResolved()
    {
        return $this->resolved;
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
        foreach ($this->grid as $lineNo => $line) {
            $lineDump = array();
            foreach ($line as $columnNo => $value) {
                $lineDump[] = static::getValueLabel($value);
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
     * Check if Grid has resolved
     */
    public function checkHasResolved()
    {
        $this->resolved = false;
        if ($this->emptyCaseCount) {
            return;
        }
        // Check rules on all lines
        for ($lineNo = 0; $lineNo < $this->height; $lineNo++) {
            $line = $this->getLineByNo($lineNo);
            if (!$this->checkLineValuesDefined($line)) {
                return;
            }
            if (!$this->checkLineValuesCollision($line)) {
                return;
            }
            if (!$this->checkLineValuesEqualities($line)) {
                return;
            }
        }
        if (!$this->checkLinesDistinctSeries($this->grid)) {
            return;
        }
        // Check rules on all columns
        $columns = array();
        for ($columnNo = 0; $columnNo < $this->width; $columnNo++) {
            $column = $this->getColumnByNo($columnNo);
            if (!$this->checkLineValuesDefined($column)) {
                return;
            }
            if (!$this->checkLineValuesCollision($column)) {
                return;
            }
            if (!$this->checkLineValuesEqualities($column)) {
                return;
            }
            $columns[$columnNo] = $column;
        }
        if (!$this->checkLinesDistinctSeries($columns)) {
            return;
        }
        $this->resolved = true;
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
     * @param $value
     * @return int
     */
    public static function getReverseValue($value)
    {
        if ($value === static::ONE) {
            return static::ZERO;
        }
        if ($value === static::ZERO) {
            return static::ONE;
        }
        throw new \InvalidArgumentException('Not exist reverse value for undefined');
    }

    /**
     * @param $value
     * @return string
     */
    public static function getValueLabel($value)
    {
        switch ($value) {
            case static::UNDEFINED:
                return '.';
                break;
            case static::ONE:
                return '1';
                break;
            case static::ZERO:
                return '0';
                break;
            default:
                throw new \UnexpectedValueException('Unknown value: ' . $value);
        }
    }

    /**
     * @param array $line
     * @return array
     */
    public static function getUndefinedLineValues(array $line)
    {
        return array_filter($line, function($value) {
            return $value === Grid::UNDEFINED;
        });
    }

    /**
     * @param array $line
     * @return array
     */
    public static function getZeroLineValues(array $line)
    {
        return array_filter($line, function($value) {
            return $value === Grid::ZERO;
        });
    }

    /**
     * @param array $line
     * @return array
     */
    public static function getOneLineValues(array $line)
    {
        return array_filter($line, function($value) {
            return $value === Grid::ONE;
        });
    }

    /**
     * @param array $line
     * @return array
     */
    public static function getUndefinedRangeLine(array $line)
    {
        $undefinedValues = static::getUndefinedLineValues($line);
        $undefinedValuePositions = array_keys($undefinedValues);

        $ranges = array();
        $firstRangeIndex = null;
        $currentRangeIndex = null;
        foreach ($undefinedValuePositions as $i => $undefinedValuePosition) {
            if (is_null($firstRangeIndex)) {
                $firstRangeIndex = $undefinedValuePosition;
                $currentRangeIndex = $undefinedValuePosition;
                continue;
            }
            if ($undefinedValuePosition === ($currentRangeIndex + 1)) {
                $currentRangeIndex++;
                continue;
            }
            $ranges[] = array(
                'min' => $firstRangeIndex,
                'max' => $currentRangeIndex
            );
            $firstRangeIndex = $undefinedValuePosition;
            $currentRangeIndex = $undefinedValuePosition;
        }
        if (!is_null($firstRangeIndex)) {
            $ranges[] = array(
                'min' => $firstRangeIndex,
                'max' => $currentRangeIndex
            );
        }
        return $ranges;
    }

    /**
     * @param array $line
     * @return array
     */
    public static function getMissingLineValueDistribution(array $line)
    {
        $count = count($line);
        $needValue = $count / 2;
        $zeroValues = static::getZeroLineValues($line);
        $oneValues = static::getOneLineValues($line);
        return array(
            static::ZERO => $needValue - count($zeroValues),
            static::ONE => $needValue - count($oneValues),
        );
    }

    /**
     * @param array $needs
     * @param array $leftRangeValues
     * @param array $rightRangeValues
     * @return array
     */
    public static function getRangePossibilities(array $needs, $leftRangeValues = array(), $rightRangeValues = array())
    {
        $rangeLength = array_sum($needs);
        if (!$rangeLength) {
            return array();
        }

        $lengthPossibilities = array();
        $rightRangeCount = count($rightRangeValues);
        $leftRangeCount = count($leftRangeValues);
        if ($leftRangeCount > 0) {
            $lengthPossibilities[] = array(
                'values' => $leftRangeValues,
                'needs' => $needs,
            );
            $rangeLength += $leftRangeCount;
            $currentLength = $leftRangeCount;
        }
        else {
            $currentLength = 1;
            if ($needs[static::ZERO]) {
                $availableValue = $needs;
                $availableValue[static::ZERO]--;
                $lengthPossibilities[] = array(
                    'values' => array(static::ZERO),
                    'needs' => $availableValue,
                );
            }
            if ($needs[static::ONE]) {
                $availableValue = $needs;
                $availableValue[static::ONE]--;
                $lengthPossibilities[] = array(
                    'values' => array(static::ONE),
                    'needs' => $availableValue,
                );
            }
        }
        do {
            if (!$lengthPossibilities) {
                break;
            }
            $nextLengthPossibilities = array();
            foreach ($lengthPossibilities as $i => $lengthPossibility) {
                $zeroAvailable = true;
                $oneAvailable = true;
                if ($currentLength > 1) {
                    $previousValue1 = $lengthPossibility['values'][$currentLength - 1];
                    $previousValue2 = $lengthPossibility['values'][$currentLength - 2];
                    if ($previousValue1 === $previousValue2) {
                        $zeroAvailable = $previousValue1 == static::ONE;
                        $oneAvailable = $previousValue1 == static::ZERO;
                    }
                }
                if ($zeroAvailable && $lengthPossibility['needs'][static::ZERO]) {
                    $newLengthPossibility = $lengthPossibility;
                    $newLengthPossibility['needs'][static::ZERO]--;
                    $newLengthPossibility['values'][] = static::ZERO;
                    $nextLengthPossibilities[] = $newLengthPossibility;
                }
                if ($oneAvailable && $lengthPossibility['needs'][static::ONE]) {
                    $newLengthPossibility = $lengthPossibility;
                    $newLengthPossibility['needs'][static::ONE]--;
                    $newLengthPossibility['values'][] = static::ONE;
                    $nextLengthPossibilities[] = $newLengthPossibility;
                }
            }
            $currentLength++;
            $lengthPossibilities = $nextLengthPossibilities;

        } while($currentLength !== $rangeLength);

        $possibilities = array();
        foreach ($lengthPossibilities as $i => $possibility) {
            if ($rightRangeCount > 0 && $currentLength > 2) {
                $previousValue1 = $possibility['values'][$currentLength - 1];
                if ($previousValue1 === $rightRangeValues[0]) {
                    $previousValue2 = $possibility['values'][$currentLength - 2];
                    if ($previousValue1 === $previousValue2) {
                        continue;
                    }
                    if ($rightRangeCount > 1 && $rightRangeValues[0] === $rightRangeValues[1]) {
                        continue;
                    }
                }
            }
            if ($leftRangeValues) {
                $possibility['values'] = array_slice($possibility['values'], $leftRangeCount);
            }
            $possibilities[] = array_values($possibility['values']);
        }
        return $possibilities;
    }

    /**
     * @param array $lines
     * @return bool
     */
    protected function checkLinesDistinctSeries(array $lines)
    {
        $convertedLines = array_map(function(array $line) {
            $line = array_map('static::getValueLabel', $line);
            return implode($line);
        }, $lines);
        return array_unique($convertedLines) === $convertedLines;
    }

    /**
     * @param array $line
     * @return bool
     */
    protected function checkLineValuesDefined(array $line)
    {
        foreach ($line as $i => $value) {
            if ($value === static::UNDEFINED) {
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
        foreach ($line as $i => $value) {
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
        foreach ($line as $i => $value) {
            if ($value === static::ZERO) {
                $equality++;
                continue;
            }
            if ($value === static::ONE) {
                $equality--;
                continue;
            }
        }
        return $equality === 0;
    }

    /**
     * @param $columnNo
     * @return array
     */
    protected function getColumnByNo($columnNo)
    {
        $this->checkGridPosition(1, $columnNo);
        $column = array();
        foreach ($this->grid as $lineNo => $line) {
            $column[$lineNo] = $line[$columnNo];
        }
        return $column;
    }

    /**
     * @param $lineNo
     * @return array
     */
    protected function getLineByNo($lineNo)
    {
        $this->checkGridPosition($lineNo, 1);
        return $this->grid[$lineNo];
    }

    /**
     * @param $side
     * @param $length
     */
    protected function checkSideLength($side, $length)
    {
        if (!is_numeric($length)) {
            throw new \InvalidArgumentException('Invalid ' . $side . ' format');
        }
        $length = (int)$length;
        if ($length <= 0) {
            throw new \InvalidArgumentException('Invalid ' . $side . ' value');
        }
        if ($length % 2 !== 0) {
            throw new \InvalidArgumentException('Invalid ' . $side . ' value');
        }
    }

    /**
     * Generate empty grid
     */
    protected function generateEmptyGrid()
    {
        $columns = array_fill(0, $this->width, static::UNDEFINED);
        $this->grid = array_fill(0, $this->height, $columns);
        $this->emptyCaseCount = $this->width * $this->height;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = array();
        foreach ($this->grid as $lineNo => $line) {
            $lineDump = array();
            foreach ($line as $columnNo => $value) {
                $lineDump[] = static::getValueLabel($value);
            }
            $result[] = implode(' ', $lineDump);
        }
        return implode("\n", $result);
    }

}