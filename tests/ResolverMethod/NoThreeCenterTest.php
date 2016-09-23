<?php

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\ResolverMethod\NoThreeCenter;

/**
 * Class Grid_Mockup
 */
class NoThreeCenterMockup extends NoThreeCenter
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
class NoThreeCenterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var NoThreeCenterMockup
     */
    protected $resolverMethod;

    public function setUp()
    {
        parent::setUp();

        $grid = new Grid();
        $this->resolverMethod = new NoThreeCenterMockup($grid);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->resolverMethod);
    }

    public function testMethodName()
    {
        $this->assertEquals('NoThreeCenter', NoThreeCenterMockup::METHOD_NAME);
    }
    
    public function testFoundOnGridLine_v1()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, -1, 1, 0]
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
                [-1, 0, -1, 0, 1, 1]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([
            2 => 1
        ], $foundValues);
    }

    public function testFoundOnGridLine_v3()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, 1, -1, 1, -1, 0, 1, 1, 0, 0]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([
            2 => 0,
        ], $foundValues);
    }

    public function testFoundOnGridLine_v4()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [-1, -1, -1, 0]
            ]
        );
        $this->assertFalse($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([], $foundValues);
    }

    public function testFoundOnGridLine_v5()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, 1, 1, -1]
            ]
        );
        $this->assertFalse($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([], $foundValues);
    }

    /**
     * @expectedException Davaxi\Takuzu\InvalidGridException
     */
    public function testCompute_withoutFound()
    {
        $this->resolverMethod->compute();
    }

}