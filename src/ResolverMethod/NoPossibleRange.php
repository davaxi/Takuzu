<?php

namespace Davaxi\Takuzu\ResolverMethod;

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\InvalidGridException;
use Davaxi\Takuzu\ResolverMethod;
use Davaxi\Takuzu\ResolverSeriesGenerator;

/**
 * Class NoPossibleRange
 * @package Davaxi\Takuzu\ResolverMethod
 */
class NoPossibleRange extends ResolverMethod
{
    const METHOD_NAME = 'NoPossibleRange';

    /**
     * @var array
     */
    protected $foundedPossibilities;

    /**
     * @var ResolverSeriesGenerator
     */
    protected $seriesGenerator;

    public function __construct(Grid $grid)
    {
        parent::__construct($grid);
        $this->seriesGenerator = new ResolverSeriesGenerator();
    }

    /**
     * @param array $line
     * @return boolean
     */
    protected function foundOnGridLine(array $line)
    {
        $undefinedRanges = static::$helpers->getUndefinedRangeLine($line);
        if (count($undefinedRanges) !== 1) {
            return false;
        }
        $length = count($line);
        $undefinedRange = $undefinedRanges[0];
        $leftValues = array();
        if ($undefinedRange['min'] > 0) {
            $leftValues = array_slice($line, 0, $undefinedRange['min']);
        }
        $rightValues = array();
        if ($undefinedRange['max'] < ($length - 1)) {
            $rightLength = $length - ($undefinedRange['max'] + 1);
            $rightValues = array_slice($line, $undefinedRange['max'] + 1, $rightLength);
        }
        $needValues = static::$helpers->getMissingLineValueDistribution($line);
        $possibilities = $this->seriesGenerator->getRangePossibilities($needValues, $leftValues, $rightValues);
        if (count($possibilities) < 1) {
            throw new InvalidGridException('Grid not solvable');
        }
        $positions = static::getPositionFromPossibilities(
            $possibilities,
            $undefinedRange['min'],
            $undefinedRange['max']
        );
        if (!$positions) {
            return false;
        }
        $this->foundedPossibilities = $possibilities;
        $this->foundedValues = $positions;
        return true;
    }

    /**
     * @param array $possibilities
     * @param $minPosition
     * @param $maxPosition
     * @return mixed
     */
    protected static function getPositionFromPossibilities(array $possibilities, $minPosition, $maxPosition)
    {
        $positions = array_fill($minPosition, $maxPosition - $minPosition + 1, null);
        foreach ($possibilities as $possibility) {
            foreach ($possibility as $positionNo => $value) {
                $realPositionNo = $positionNo + $minPosition;
                if (!array_key_exists($realPositionNo, $positions)) {
                    continue;
                }
                if ($positions[$realPositionNo] === null) {
                    $positions[$realPositionNo] = $value;
                    continue;
                }
                if ($positions[$realPositionNo] !== $value) {
                    unset($positions[$realPositionNo]);
                    continue;
                }
            }
        }
        return $positions;
    }
}