<?php


use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\GridHelpers;

class GridHelpersTest extends PHPUnit_Framework_TestCase
{

    public function testGetReverseValue_One()
    {
        $result = GridHelpers::getReverseValue(Grid::ONE);
        $this->assertEquals(Grid::ZERO, $result);
    }

    public function testGetReverseValue_Zero()
    {
        $result = GridHelpers::getReverseValue(Grid::ZERO);
        $this->assertEquals(Grid::ONE, $result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetReverseValue_Undefined()
    {
        GridHelpers::getReverseValue(Grid::UNDEFINED);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetReverseValue_InvalidValue()
    {
        GridHelpers::getReverseValue('invalid value');
    }

    public function testGetValueLabel_One()
    {
        $result = GridHelpers::getValueLabel(Grid::ONE);
        $this->assertEquals('1', $result);
    }

    public function testGetValueLabel_Zero()
    {
        $result = GridHelpers::getValueLabel(Grid::ZERO);
        $this->assertEquals('0', $result);
    }

    public function testGetValueLabel_Undefined()
    {
        $result = GridHelpers::getValueLabel(Grid::UNDEFINED);
        $this->assertEquals('.', $result);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testGetValueLabel_InvalidValue()
    {
        GridHelpers::getValueLabel('Invalid value');
    }

    public function testGetMissingLineValueDistribution_empty()
    {
        $result = GridHelpers::getMissingLineValueDistribution([]);
        $this->assertEquals([0 => 0, 1 => 0], $result);
    }

    public function testGetMissingLineValueDistribution_complete()
    {
        $result = GridHelpers::getMissingLineValueDistribution([0, 1, 1, 0]);
        $this->assertEquals([0 => 0, 1 => 0], $result);
    }

    public function testGetMissingLineValueDistribution()
    {
        $result = GridHelpers::getMissingLineValueDistribution([0, 1, 1, -1, -1, 0, -1, 0, -1, -1]);
        $this->assertEquals([0 => 2, 1 => 3], $result);
    }

    public function testGetUndefinedRangeLine()
    {
        $result = GridHelpers::getUndefinedRangeLine([-1, 0, 1, -1, -1, 0, 1, -1, -1, -1]);
        $this->assertEquals(
            [
                ['min' => 0, 'max' => 0],
                ['min' => 3, 'max' => 4],
                ['min' => 7, 'max' => 9],
            ],
            $result
        );
    }

    public function testGetUndefinedLinePositions_empty()
    {
        $result = GridHelpers::getUndefinedLinePositions([]);
        $this->assertEquals([], $result);
    }

    public function testGetUndefinedLinePositions_complete()
    {
        $result = GridHelpers::getUndefinedLinePositions([0, 1, 0, 1]);
        $this->assertEquals([], $result);
    }

    public function testGetUndefinedLinePositions()
    {
        $result = GridHelpers::getUndefinedLinePositions([-1, 1, -1, -1, 0, 0, -1, -1]);
        $this->assertEquals([0, 2, 3, 6, 7], $result);
    }

    public function testGetUndefinedLineValues()
    {
        $result = GridHelpers::getUndefinedLineValues([-1, 1, -1, -1, 0, 0, -1, -1]);
        $this->assertEquals([0 => -1, 2 => -1, 3 => -1, 6 => -1, 7 => -1], $result);
    }

    public function testGetZeroLineValues()
    {
        $result = GridHelpers::getZeroLineValues([-1, 1, -1, -1, 0, 0, -1, -1]);
        $this->assertEquals([4 => 0, 5 => 0], $result);
    }

    public function testGetOneLineValues()
    {
        $result = GridHelpers::getOneLineValues([-1, 1, -1, -1, 0, 0, -1, -1]);
        $this->assertEquals([1 => 1], $result);
    }

}
