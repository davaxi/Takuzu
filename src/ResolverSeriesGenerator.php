<?php

namespace Davaxi\Takuzu;

/**
 * Class ResolverGenerator
 * @package Davaxi\Takuzu
 */
class ResolverSeriesGenerator
{
    /**
     * @var array
     */
    protected static $values = array(
        Grid::ZERO,
        Grid::ONE
    );

    /**
     * @param array $needs
     * @param array $leftValues
     * @param array $rightValues
     * @return array
     */
    public static function getRangePossibilities(array $needs, $leftValues = array(), $rightValues = array())
    {
        $rangeLength = array_sum($needs);
        if (!$rangeLength) {
            return array();
        }

        $possibility = array(
            'needs' => $needs,
            'needsCount' => array_sum($needs),
            'values' => $leftValues,
        );
        $possibilities = static::getPossibilities($possibility);
        while ($possibilities && $possibilities[0]['needsCount']) {
            $newPossibilities = array();
            foreach ($possibilities as $possibility) {
                $newPossibilities = array_merge(
                    $newPossibilities,
                    static::getPossibilities($possibility)
                );
            }
            $possibilities = $newPossibilities;

        }
        $rightValues = array_slice($rightValues, 0, 2);
        foreach ($rightValues as $rightValue) {
            $newPossibilities = array();
            foreach ($possibilities as $possibility) {
                $possibility['needs'][$rightValue] = 1;
                $newPossibilities = array_merge(
                    $newPossibilities,
                    static::getPossibilities($possibility)
                );
            }
            $possibilities = $newPossibilities;
        }
        return array_map(
            function(array $possibility) use ($leftValues, $rangeLength) {
                return array_slice(
                    $possibility['values'],
                    count($leftValues),
                    $rangeLength
                );
            },
            $possibilities
        );
    }

    /**
     * @param array $possibility
     * @return array
     */
    protected static function getPossibilities(array $possibility)
    {
        $possibilities = array();
        foreach (static::$values as $value) {
            if (!$possibility['needs'][$value]) {
                continue;
            }
            if (!static::checkRange($possibility['values'], $value)) {
                continue;
            }
            $possibilities[] = static::getPossibility(
                $possibility['values'],
                $possibility['needs'],
                $value
            );
        }
        return $possibilities;
    }

    /**
     * @param array $values
     * @param array $needs
     * @param $useValue
     * @return array
     */
    protected static function getPossibility(array $values, array $needs, $useValue)
    {
        if ($needs[$useValue] <= 0) {
            throw new \LogicException('Invalid call to method');
        }
        $needs[$useValue]--;
        $values[] = $useValue;
        return array(
            'values' => $values,
            'needs' => $needs,
            'needsCount' => array_sum($needs),
        );
    }

    /**
     * @param $values
     * @param $newValue
     * @return bool
     */
    protected static function checkRange($values, $newValue)
    {
        $length = count($values);
        if ($length > 1) {
            $lastValue = array_pop($values);
            $previousValue = array_pop($values);
            if ($lastValue !== $previousValue) {
                return true;
            }
            return $lastValue !== $newValue;
        }
        return true;
    }

}