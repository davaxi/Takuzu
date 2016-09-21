<?php

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\ResolverMethod\NoPossibleRange;

/**
 * Class Grid_Mockup
 */
class NoPossibleRangeMockup extends NoPossibleRange
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
class NoPossibleRangeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var NoPossibleRangeMockup
     */
    protected $resolverMethod;

    public function setUp()
    {
        parent::setUp();

        $grid = new Grid();
        $this->resolverMethod = new NoPossibleRangeMockup($grid);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->resolverMethod);
    }

    public function testMethodName()
    {
        $this->assertEquals('NoPossibleRange', NoPossibleRangeMockup::METHOD_NAME);
    }

    public function testHasSeriesGenerator()
    {
        $this->assertClassHasAttribute('seriesGenerator', 'Davaxi\Takuzu\ResolverMethod\NoPossibleRange');
        $generator = $this->resolverMethod->getAttribute('seriesGenerator');
        $this->assertInstanceOf('Davaxi\Takuzu\ResolverSeriesGenerator', $generator);
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
                [0, -1, -1, 1]
            ]
        );
        $this->assertFalse($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([], $foundValues);
    }

    public function testFoundOnGridLine_v3()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, 1, -1, -1, -1, 1, 0, 1, 0, 0]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([
            2 => 1,
            3 => 0,
            4 => 1,
        ], $foundValues);
    }

    public function testFoundOnGridLine_v4()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, -1, -1, -1, 1, 0]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([
            1 => 1,

        ], $foundValues);
    }

    public function testFoundOnGridLine_v5()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, -1, -1, -1, -1, -1, 1, 0]
            ]
        );
        $this->assertFalse($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([], $foundValues);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testFoundOnGridLine_invalidSeries()
    {
        $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, -1, -1, -1, 0, 1, 0, 0]
            ]
        );
    }


}