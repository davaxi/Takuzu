<?php

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\ResolverMethod\NecessaryValues;

/**
 * Class Grid_Mockup
 */
class NecessaryValuesMockup extends NecessaryValues
{
    /**
     * @param $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return $this->$attribute;
    }

    /**
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function useProtectedMethod($method, array $params)
    {
        return call_user_func_array(array($this, $method), $params);
    }
}

/**
 * Class GridTest
 */
class NecessaryValuesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var NecessaryValuesMockup
     */
    protected $resolverMethod;

    public function setUp()
    {
        parent::setUp();

        $grid = new Grid();
        $this->resolverMethod = new NecessaryValuesMockup($grid);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->resolverMethod);
    }

    public function testMethodName()
    {
        $this->assertEquals('NecessaryValues', NecessaryValuesMockup::METHOD_NAME);
    }

    public function testFoundOnGridLine_v1()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, -1, 1, 0, 1, -1, -1, 1, -1, -1]
            ]
        );
        $this->assertFalse($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([], $foundValues);
    }

    public function testFoundOnGridLine_v2()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, -1, -1, -1, 0, -1]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([ 5 => 1 ], $foundValues);
    }


    public function testFoundOnGridLine_v3()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [1, -1, -1, -1, 1, 0, 1, -1, -1 , 1]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([ 7 => 0, 8 => 0 ], $foundValues);
    }

    /**
     * @expectedException  Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckExistNecessaryValues_invalidZero()
    {
        $undefinedRanges = array();
        $needsValues = array(
            0 => -1,
            1 => 0,
        );
        $this->resolverMethod->useProtectedMethod(
            'checkExistNecessaryValues',
            [&$needsValues, &$undefinedRanges]
        );
    }

    /**
     * @expectedException Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckExistNecessaryValues_invalidOne()
    {
        $undefinedRanges = array();
        $needsValues = array(
            0 => 1,
            1 => -1,
        );
        $this->resolverMethod->useProtectedMethod(
            'checkExistNecessaryValues',
            [&$needsValues, &$undefinedRanges]
        );
    }

    public function testCheckExistNecessaryValues_withoutRange()
    {
        $undefinedRanges = array();
        $needsValues = array(
            0 => 0,
            1 => 1,
        );
        $result = $this->resolverMethod->useProtectedMethod(
            'checkExistNecessaryValues',
            [&$needsValues, &$undefinedRanges]
        );
        $this->assertFalse($result);
    }

    public function testGetNoPossibleRangeValueMin()
    {
        $expectedValues = array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 1,
            4 => 1,
            10 => 3,
            11 => 3
        );
        foreach ($expectedValues as $assert => $expected) {
            $result = $this->resolverMethod->useProtectedMethod(
                'getNoPossibleRangeValueMin',
                [$assert]
            );
            $this->assertEquals($expected, $result, $assert);
        }
    }

    public function testGetNoPossibleRangeReverseValueMin()
    {
        $expectedValues = array(
            0 => 0,
            1 => 0,
            2 => 1,
            3 => 1,
            4 => 1,
            10 => 3,
            11 => 4,
        );
        foreach ($expectedValues as $assert => $expected) {
            $result = $this->resolverMethod->useProtectedMethod(
                'getNoPossibleRangeReverseValueMin',
                [$assert]
            );
            $this->assertEquals($expected, $result, $assert);
        }
    }

    /**
     * @expectedException Davaxi\Takuzu\InvalidGridException
     */
    public function testCompute_withoutFound()
    {
        $this->resolverMethod->compute();
    }

}