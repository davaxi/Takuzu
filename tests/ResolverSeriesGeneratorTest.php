<?php

use Davaxi\Takuzu\ResolverSeriesGenerator;

/**
 * Class GridChecker_Mockup
 */
class ResolverSeriesGenerator_Mockup extends ResolverSeriesGenerator
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
 * Class GridCheckerTest
 */
class ResolverSeriesGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ResolverSeriesGenerator_Mockup
     */
    protected $resolverSeriesGenerator;

    public function setUp()
    {
        parent::setUp();
        $this->resolverSeriesGenerator = new ResolverSeriesGenerator_Mockup();
    }

    public function tearDown()
    {
        unset($this->resolverSeriesGenerator);
        parent::tearDown();
    }

    public function testGetRangePossibilities_Empty()
    {
        $result = ResolverSeriesGenerator_Mockup::getRangePossibilities([], [], []);
        $this->assertEquals([], $result);
    }

    /**
     * @expectedException LogicException
     */
    public function testGetPossibility_NegativeValue()
    {
        $this->resolverSeriesGenerator->useProtectedMethod(
            'getPossibility',
            [
                [],
                [0 => -1, 1 => 1],
                0
            ]
        );
    }

    /**
     * @expectedException LogicException
     */
    public function testGetPossibility_NullValue()
    {
        $this->resolverSeriesGenerator->useProtectedMethod(
            'getPossibility',
            [
                [],
                [0 => 1, 1 => 0],
                1
            ]
        );
    }
}
