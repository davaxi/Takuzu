<?php

namespace Davaxi\Takuzu\ResolverMethod;

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\ResolverMethod;

/**
 * Class RandomValue
 * @package Davaxi\Takuzu\ResolverMethod
 */
class RandomValue extends ResolverMethod
{
    const METHOD_NAME = 'RandomValue';
    const COST = 15;

    public function __construct(Grid $grid)
    {
        parent::__construct($grid);

        $this->founded = true;
        $this->foundedLineType = static::TYPE_LINE;
    }

    /**
     * @param array $line
     * @return boolean
     */
    protected function foundOnGridLine(array $line)
    {
        return count($line) > 0;
    }

    /**
     * @param $lineNo
     * @param $columnNo
     * @param $value
     */
    public function setValue($lineNo, $columnNo, $value)
    {
        $this->foundedLineNo = $lineNo;
        $this->foundedValues[$columnNo] = $value;
    }

}