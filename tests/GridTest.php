<?php

use Davaxi\Takuzu\Grid;

/**
 * Class Grid_Mockup
 */
class Grid_Mockup extends Grid
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
        return call_user_func_array($this->$method, $params);
    }
}

/**
 * Class GridTest
 */
class GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Grid_Mockup
     */
    protected $grid;

    public function setUp()
    {
        parent::setUp();
        $this->grid = new Grid_Mockup();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->grid);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_InvalidWidthString()
    {
        new Grid_Mockup('invalid width', 2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_InvalidWidthNumeric()
    {
        new Grid_Mockup(-2, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_InvalidWidthRatio()
    {
        new Grid_Mockup(3, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_MissingWidth()
    {
        new Grid_Mockup(null, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_InvalidHeightString()
    {
        new Grid_Mockup(2, 'invalid width');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_InvalidHeightNumeric()
    {
        new Grid_Mockup(2, -2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_InvalidHeightRatio()
    {
        new Grid_Mockup(2, 3);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstruct_MissingHeight()
    {
        new Grid_Mockup(2);
    }

    public function testConstruct_Empty()
    {
        $this->grid = new Grid_Mockup();
        $this->assertEquals(array(), $this->grid->getAttribute('grid'));
        $this->assertEquals(0, $this->grid->getAttribute('width'));
        $this->assertEquals(0, $this->grid->getAttribute('height'));
        $this->assertEquals(false, $this->grid->getAttribute('resolved'));
        $this->assertEquals(0, $this->grid->getAttribute('emptyCaseCount'));
    }

    public function testConstruct()
    {
        $this->grid = new Grid_Mockup(2, 4);
        $expectedGrid = array(
            array(Grid::UNDEFINED, Grid::UNDEFINED),
            array(Grid::UNDEFINED, Grid::UNDEFINED),
            array(Grid::UNDEFINED, Grid::UNDEFINED),
            array(Grid::UNDEFINED, Grid::UNDEFINED),
        );
        $this->assertEquals($expectedGrid, $this->grid->getAttribute('grid'));
        $this->assertEquals(2, $this->grid->getAttribute('width'));
        $this->assertEquals(4, $this->grid->getAttribute('height'));
        $this->assertEquals(false, $this->grid->getAttribute('resolved'));
        $this->assertEquals(8, $this->grid->getAttribute('emptyCaseCount'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetGridFromString_InvalidString()
    {
        $this->grid->setGridFromString('InvalidString');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetGridFromString_InvalidLineCount()
    {
        $this->grid->setGridFromString('..1..0');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetGridFromString_InvalidColumnCount()
    {
        $this->grid->setGridFromString("..1..\n00.10");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetGridFromString_InvalidColumnPersistenceCount()
    {
        $this->grid->setGridFromString("1.\n1.0");
    }

    public function testSetGridFromStringSquare()
    {
        $this->grid->setGridFromString("0.\n.0");
        $expected = array(
            array(Grid::ZERO, Grid::UNDEFINED),
            array(Grid::UNDEFINED, Grid::ZERO),
        );
        $result = $this->grid->getAttribute('grid');
        $this->assertEquals($expected, $result);
    }

    public function testSetGridFromStringRectangle()
    {
        $this->grid->setGridFromString("0 1 \n11  ");
        $expected = array(
            array(Grid::ZERO, Grid::UNDEFINED, Grid::ONE, Grid::UNDEFINED),
            array(Grid::ONE, Grid::ONE, Grid::UNDEFINED, Grid::UNDEFINED),
        );
        $result = $this->grid->getAttribute('grid');
        $this->assertEquals($expected, $result);
    }
}