<?php

namespace Davaxi\Takuzu;

/**
 * Class GridHelpers
 * @package Davaxi\Takuzu
 */
class GridHelpers
{
    /**
     * @param $value
     * @return int
     */
    public static function getReverseValue($value)
    {
        if ($value === Grid::ONE) {
            return Grid::ZERO;
        }
        if ($value === Grid::ZERO) {
            return Grid::ONE;
        }
        throw new \InvalidArgumentException('Not exist reverse value for undefined');
    }

    /**
     * @param $value
     * @return string
     */
    public static function getValueLabel($value)
    {
        if ($value === Grid::ONE) {
            return '1';
        }
        if ($value === Grid::ZERO) {
            return '0';
        }
        if ($value === Grid::UNDEFINED) {
            return '.';
        }
        throw new \UnexpectedValueException('Unknown value: ' . $value);
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
            Grid::ZERO => $needValue - count($zeroValues),
            Grid::ONE => $needValue - count($oneValues),
        );
    }

    /**
     * @param array $line
     * @return array
     */
    public static function getUndefinedRangeLine(array $line)
    {
        $valuesPositions = static::getUndefinedLinePositions($line);

        $ranges = array();
        $firstRangeIndex = null;
        $currentRangeIndex = null;
        foreach ($valuesPositions as $valuePosition) {
            if (is_null($firstRangeIndex)) {
                $firstRangeIndex = $valuePosition;
                $currentRangeIndex = $valuePosition;
                continue;
            }
            if ($valuePosition === ($currentRangeIndex + 1)) {
                $currentRangeIndex++;
                continue;
            }
            $ranges[] = array(
                'min' => $firstRangeIndex,
                'max' => $currentRangeIndex
            );
            $firstRangeIndex = $valuePosition;
            $currentRangeIndex = $valuePosition;
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
     * @return mixed
     */
    public static function getUndefinedLinePositions(array $line)
    {
        $values = static::getUndefinedLineValues($line);
        return array_keys($values);
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


}