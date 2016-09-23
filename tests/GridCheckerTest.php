<?php

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\GridChecker;
use Davaxi\Takuzu\InvalidGridException;


/**
 * Class GridChecker_Mockup
 */
class GridChecker_Mockup extends GridChecker
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
class GridCheckerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GridChecker_Mockup
     */
    protected $checker;

    public function setUp()
    {
        parent::setUp();
        $grid = new Grid();
        $this->checker = new GridChecker_Mockup($grid);
    }

    public function tearDown()
    {
        unset($this->helpers);
        parent::tearDown();
    }

    public function testCheckLines_withUnDefined()
    {
        $line = [[0, -1, 0]];
        $result = $this->checker->useProtectedMethod(
            'checkLines', [&$line]
        );
        $this->assertFalse($result);
    }

    public function testCheckLines_withCollision()
    {
        $line = [[1, 1, 0, 0, 0, 1]];
        $result = $this->checker->useProtectedMethod(
            'checkLines', [&$line]
        );
        $this->assertFalse($result);
    }

    public function testCheckLines_withNoEqualities()
    {
        $line = [[0, 1, 0, 0]];
        $result = $this->checker->useProtectedMethod(
            'checkLines', [&$line]
        );
        $this->assertFalse($result);
    }

    public function testCheckLines_withNoDistincSeries()
    {
        $line = [
            [1, 1, 0, 0],
            [1, 1, 0, 0],
        ];
        $result = $this->checker->useProtectedMethod(
            'checkLines', [&$line]
        );
        $this->assertFalse($result);
    }

    public function testCheckLines()
    {
        $line = [
            [1, 1, 0, 0],
            [1, 0, 1, 0],
        ];
        $result = $this->checker->useProtectedMethod(
            'checkLines', [&$line]
        );
        $this->assertTrue($result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCheckSideLength_invalidLength()
    {
        $this->checker->useProtectedMethod(
            'checkSideLength',
            ['side', 'invalid length']
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCheckSideLength_negativeLength()
    {
        $this->checker->useProtectedMethod(
            'checkSideLength',
            ['side', -1]
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCheckSideLength_DoubleLength()
    {
        $this->checker->useProtectedMethod(
            'checkSideLength',
            ['side', 3]
        );
    }

    public function testCheckSideLength()
    {
        $this->checker->useProtectedMethod(
            'checkSideLength',
            ['side', 2]
        );
        $this->assertTrue(true);
    }

    public function testHasResolved_Default()
    {
        $result = $this->checker->hasResolved();
        $this->assertFalse($result);
    }

    public function testCheckHasResolved()
    {
        $grid = new Grid();
        $grid->setGridFromArray(
            [
                [0, 1],
                [1, 0]
            ]
        );
        $this->checker = new GridChecker_Mockup($grid);
        $this->checker->checkHasResolved();
        $result = $this->checker->hasResolved();
        $this->assertTrue($result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCheckGridValue_invalid()
    {
        $this->checker->checkGridValue('invalid value');
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckGridPosition_invalidLineNoNegative()
    {
        $grid = new Grid();
        $grid->setGridFromArray([[0, 1], [1, 0]]);
        $this->checker = new GridChecker($grid);
        $this->checker->checkGridPosition(-1, 0);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckGridPosition_invalidLineNoOutOfRange()
    {
        $grid = new Grid();
        $grid->setGridFromArray([[0, 1], [1, 0]]);
        $this->checker = new GridChecker($grid);
        $this->checker->checkGridPosition(2, 0);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckGridPosition_invalidLineColumnNoNegative()
    {
        $grid = new Grid();
        $grid->setGridFromArray([[0, 1], [1, 0]]);
        $this->checker = new GridChecker($grid);
        $this->checker->checkGridPosition(1, -1);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckGridPosition_invalidLineColumnNoOutOfRange()
    {
        $grid = new Grid();
        $grid->setGridFromArray([[0, 1], [1, 0]]);
        $this->checker = new GridChecker($grid);
        $this->checker->checkGridPosition(1, 2);
    }

    public function testCheckGridPosition()
    {
        $grid = new Grid();
        $grid->setGridFromArray([[0, 1], [1, 0]]);
        $this->checker = new GridChecker($grid);
        $this->checker->checkGridPosition(1, 1);
        $this->assertTrue(true);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckSideLength_invalidColumn()
    {
        $this->checker->checkSides('invalid', 2);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckSideLength_invalidColumnNegative()
    {
        $this->checker->checkSides(-1, 2);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckSideLength_invalidColumnDouble()
    {
        $this->checker->checkSides(3, 2);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckSideLength_invalidLine()
    {
        $this->checker->checkSides(2, 'invalid value');
    }


    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckSideLength_invalidLineNegative()
    {
        $this->checker->checkSides(2, -1);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testCheckSideLength_invalidLineDouble()
    {
        $this->checker->checkSides(2, 3);
    }

    public function testCheckSideLengt()
    {
        $this->checker->checkSides(2, 4);
        $this->assertTrue(true);
    }




}