<?php

namespace Davaxi\Takuzu\ResolverMethod;

use Davaxi\Takuzu\GridHelpers;
use Davaxi\Takuzu\ResolverMethod;

/**
 * Class CompleteEquality
 * @package Davaxi\Takuzu\ResolverMethod
 */
class CompleteEquality extends ResolverMethod
{
    const METHOD_NAME = 'CompleteEquality';

    /**
     * @param array $line
     * @return boolean
     */
    protected function foundOnGridLine(array $line)
    {
        $needs = GridHelpers::getMissingLineValueDistribution($line);
        foreach ($needs as $value => $needCount) {
            if ($needCount) {
                continue;
            }
            $this->foundedValues = array_fill_keys(
                GridHelpers::getUndefinedLinePositions($line),
                GridHelpers::getReverseValue($value)
            );
            return true;
        }
        return false;
    }
}