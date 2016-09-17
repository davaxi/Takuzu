<?php

namespace Davaxi\Takuzu;

/**
 * Class ResolverGenerator
 * @package Davaxi\Takuzu
 */
class ResolverSeriesGenerator
{
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
            if ($needs[Grid::ZERO]) {
                $availableValue = $needs;
                $availableValue[Grid::ZERO]--;

                $lengthPossibilities[] = array(
                    'values' => array(Grid::ZERO),
                    'needs' => $availableValue,
                );
            }
            if ($needs[Grid::ONE]) {
                $availableValue = $needs;
                $availableValue[Grid::ONE]--;
                $lengthPossibilities[] = array(
                    'values' => array(Grid::ONE),
                    'needs' => $availableValue,
                );
            }
        }
        do {
            if (!$lengthPossibilities) {
                break;
            }
            $nextLengthPossibilities = array();
            foreach ($lengthPossibilities as $lengthPossibility) {
                $zeroAvailable = true;
                $oneAvailable = true;
                if ($currentLength > 1) {
                    $previousValue1 = $lengthPossibility['values'][$currentLength - 1];
                    $previousValue2 = $lengthPossibility['values'][$currentLength - 2];
                    if ($previousValue1 === $previousValue2) {
                        $zeroAvailable = $previousValue1 == Grid::ONE;
                        $oneAvailable = $previousValue1 == Grid::ZERO;
                    }
                }
                if ($zeroAvailable && $lengthPossibility['needs'][Grid::ZERO]) {
                    $newLengthPossibility = $lengthPossibility;
                    $newLengthPossibility['needs'][Grid::ZERO]--;
                    $newLengthPossibility['values'][] = Grid::ZERO;
                    $nextLengthPossibilities[] = $newLengthPossibility;
                }

                if ($oneAvailable && $lengthPossibility['needs'][Grid::ONE]) {
                    $newLengthPossibility = $lengthPossibility;
                    $newLengthPossibility['needs'][Grid::ONE]--;
                    $newLengthPossibility['values'][] = Grid::ONE;
                    $nextLengthPossibilities[] = $newLengthPossibility;
                }
            }
            $currentLength++;
            $lengthPossibilities = $nextLengthPossibilities;

        } while($currentLength !== $rangeLength);

        $possibilities = array();
        foreach ($lengthPossibilities as $possibility) {
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

}