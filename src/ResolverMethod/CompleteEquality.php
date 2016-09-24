<?php

namespace Davaxi\Takuzu\ResolverMethod;

use Davaxi\Takuzu\ResolverMethod;

/**
 * Class CompleteEquality
 * @package Davaxi\Takuzu\ResolverMethod
 */
class CompleteEquality extends ResolverMethod
{
    const METHOD_NAME = 'CompleteEquality';
    const COST = 3;

    /**
     * @param array $line
     * @return boolean
     */
    protected function foundOnGridLine(array $line)
    {
        $needs = static::$helpers->getMissingLineValueDistribution($line);
        foreach ($needs as $value => $needCount) {
            if ($needCount) {
                continue;
            }
            $this->foundedValues = array_fill_keys(
                static::$helpers->getUndefinedLinePositions($line),
                static::$helpers->getReverseValue($value)
            );
            return true;
        }
        return false;
    }
}