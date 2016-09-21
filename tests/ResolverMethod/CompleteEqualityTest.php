<?php

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\ResolverMethod\CompleteEquality;

/**
 * Class Grid_Mockup
 */
class CompleteEqualityMockup extends CompleteEquality
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
class CompleteEqualityTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var CompleteEqualityMockup
     */
    protected $resolverMethod;

    public function setUp()
    {
        parent::setUp();

        $grid = new Grid();
        $this->resolverMethod = new CompleteEqualityMockup($grid);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->resolverMethod);
    }

    public function testMethodName()
    {
        $this->assertEquals('CompleteEquality', CompleteEqualityMockup::METHOD_NAME);
    }

    public function testFoundOnGridLine_v1()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, 1, 0, -1, -1, -1]
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
                [0, 1, 0, 0, -1, 1]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([ 4 => 1 ], $foundValues);
    }

    public function testFoundOnGridLine_v3()
    {
        $result = $this->resolverMethod->useProtectedMethod(
            'foundOnGridLine',
            [
                [0, -1, 0, 0, -1, -1]
            ]
        );
        $this->assertTrue($result);
        $foundValues = $this->resolverMethod->getAttribute('foundedValues');
        $this->assertEquals([
            1 => 1,
            4 => 1,
            5 => 1
        ], $foundValues);
    }
}