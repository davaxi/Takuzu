<?php

namespace Davaxi\Takuzu\ResolverMethod;

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\InvalidGridException;
use Davaxi\Takuzu\ResolverMethod;

/**
 * Class NecessaryValues
 * @package Davaxi\Takuzu\ResolverMethod
 */
class NecessaryValues extends ResolverMethod
{
    const METHOD_NAME = 'NecessaryValues';

    /**
     * @var array
     */
    protected $undefinedRanges;

    /**
     * @var array
     */
    protected $needValues;

    /**
     * @param array $line
     * @return boolean
     */
    protected function foundOnGridLine(array $line)
    {
        $count = count($line);
        $needValues = static::$helpers->getMissingLineValueDistribution($line);
        $undefinedRanges = static::$helpers->getUndefinedRangeLine($line);
        foreach ($undefinedRanges as &$range) {
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
                $range['needs'] = static::getUpdatedNeedRangeValue(
                    $range['needs'],
                    $rangeLength,
                    $line[$range['min'] - 1]
                );
            }
            if ($range['max'] < ($count - 1)) {
                $range['needs'] = static::getUpdatedNeedRangeValue(
                    $range['needs'],
                    $rangeLength,
                    $line[$range['max'] + 1]
                );
            }
            $needValues[Grid::ZERO] -= $range['needs'][Grid::ZERO];
            $needValues[Grid::ONE] -= $range['needs'][Grid::ONE];
            unset($range);
        }

        return $this->checkExistNecessaryValues($needValues, $undefinedRanges);
    }

    /**
     * @param array $needValues
     * @param array $undefinedRanges
     * @return bool
     */
    protected function checkExistNecessaryValues(array &$needValues, array &$undefinedRanges)
    {
        if ($needValues[Grid::ZERO] < 0 || $needValues[Grid::ONE] < 0) {
            throw new InvalidGridException('Grid line not to be resolved.');
        }

        foreach (static::$values as $value) {
            if ($needValues[$value] > 0) {
                continue;
            }
            foreach ($undefinedRanges as $range) {
                if ($range['needs'][$value] !== 0) {
                    continue;
                }
                for ($position = $range['min']; $position <= $range['max']; $position++) {
                    $this->foundedValues[$position] = static::$helpers->getReverseValue($value);
                }
            }
            if ($this->foundedValues) {
                $this->needValues = $needValues;
                $this->undefinedRanges = $undefinedRanges;
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $rangeNeeds
     * @param $rangeLength
     * @param $sideValue
     * @return array
     */
    protected static function getUpdatedNeedRangeValue(array $rangeNeeds, $rangeLength, $sideValue)
    {
        $rangeNeeds[$sideValue] = max(
            $rangeNeeds[$sideValue],
            static::getNoPossibleRangeValueMin($rangeLength)
        );
        $reverseSideValue = static::$helpers->getReverseValue($sideValue);
        $rangeNeeds[$reverseSideValue] = max(
            $rangeNeeds[$reverseSideValue],
            static::getNoPossibleRangeReverseValueMin($rangeLength)
        );
        return $rangeNeeds;
    }

    /**
     * @param $rangeLength
     * @return int
     */
    protected static function getNoPossibleRangeValueMin($rangeLength)
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
    protected static function getNoPossibleRangeReverseValueMin($rangeLength)
    {
        if ($rangeLength < 2) {
            return 0;
        }
        return (int)floor(($rangeLength + 1) / 3);
    }
}