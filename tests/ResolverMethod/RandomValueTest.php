<?php

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\ResolverMethod\RandomValue;

/**
 * Class Grid_Mockup
 */
class RandomValueMockup extends RandomValue
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
class RandomValueTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RandomValueMockup
     */
    protected $resolverMethod;

    public function setUp()
    {
        parent::setUp();

        $grid = new Grid();
        $this->resolverMethod = new RandomValueMockup($grid);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->resolverMethod);
    }

    public function testGetOriginal()
    {
        $grid = new Grid();
        $this->resolverMethod = new RandomValueMockup($grid);
        $originalGrid = $this->resolverMethod->getOriginalGrid();
        $this->assertSame($grid, $originalGrid);
    }

    public function testMethodName()
    {
        $this->assertEquals('RandomValue', RandomValueMockup::METHOD_NAME);
    }

    public function testHasFounded()
    {
        $result = $this->resolverMethod->getAttribute('founded');
        $this->assertTrue($result);
        $result = $this->resolverMethod->getAttribute('foundedLineType');
        $this->assertEquals(RandomValue::TYPE_LINE, $result);
    }

    public function testFoundOnGridLine_noLine()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                []
            ]
        );
        $this->assertFalse($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([], $foundValues);
    }

    public function testFoundOnGridLine()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, 0, -1, 0, 1, 1]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([], $foundValues);
    }

    /**
     * @expectedException Davaxi\Takuzu\InvalidGridException
     */
    public function testCompute_withoutSet()
    {
        $this->resolverMethod->compute();
    }

    public function testSetValue()
    {
        $this->resolverMethod->setValue(1, 2, 3);
        $lineNo = $this->resolverMethod->getAttribute('foundedLineNo');
        $this->assertEquals(1, $lineNo);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([2 => 3], $foundValues);
    }
}