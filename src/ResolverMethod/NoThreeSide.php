<?php

namespace Davaxi\Takuzu\ResolverMethod;

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\GridHelpers;
use Davaxi\Takuzu\ResolverMethod;

/**
 * Class NoThreeSide
 * @package Davaxi\Takuzu\ResolverMethod
 */
class NoThreeSide extends ResolverMethod
{
    const METHOD_NAME = 'NoThreeSide';

    const WAY_BEFORE = 1;
    const WAY_AFTER = 2;

    /**
     * @var integer
     */
    protected $foundedWay;

    /**
     * @param array $line
     * @return boolean
     */
    protected function foundOnGridLine(array $line)
    {
        $positions = GridHelpers::getUndefinedLinePositions($line);
        foreach ($positions as $position) {
            if (static::checkLineRange($line, $position, -1)) {
                $this->foundedWay = static::WAY_BEFORE;
                return true;
            }
            if (static::checkLineRange($line, $position, 1)) {
                $this->foundedWay = static::WAY_AFTER;
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $line
     * @param $position
     * @param $order
     * @return bool
     */
    protected function checkLineRange(array $line, $position, $order)
    {
        $firstPosition = $position + (1 * $order);
        if (!isset($line[$firstPosition])) {
            return false;
        }
        $secondPosition = $position + (2 * $order);
        if (!isset($line[$secondPosition])) {
            return false;
        }
        if ($line[$firstPosition] !== $line[$secondPosition]) {
            return false;
        }
        if ($line[$firstPosition] === Grid::UNDEFINED) {
            return false;
        }
        $this->foundedValues[$position] = GridHelpers::getReverseValue($line[$firstPosition]);
        return true;
    }

}