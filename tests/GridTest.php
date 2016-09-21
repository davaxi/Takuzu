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

    public function testClone()
    {
        $original = new Grid_Mockup(10, 6);
        $copy = clone $original;
        $this->assertEquals($original->getWidth(), $copy->getWidth());
        $this->assertEquals($original->getHeight(), $copy->getHeight());
        $this->assertEquals($original->getEmptyCaseCount(), $copy->getEmptyCaseCount());
        $this->assertEquals($original->getGridArray(), $copy->getGridArray());
        $this->assertSame($original->getHelpers(), $copy->getHelpers());
        $this->assertNotSame($original->getChecker(), $copy->getChecker());
    }

    public function testConstruct_Empty()
    {
        $this->grid = new Grid_Mockup();
        $this->assertEquals(array(), $this->grid->getAttribute('grid'));
        $this->assertEquals(0, $this->grid->getAttribute('width'));
        $this->assertEquals(0, $this->grid->getAttribute('height'));
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
        $this->assertEquals(8, $this->grid->getAttribute('emptyCaseCount'));
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testSetGridValue_InvalidLinePosition()
    {
        $this->grid = new Grid_Mockup(2, 4);
        $this->grid->setGridValue(5, 1, Grid::ONE);
    }

    /**
     * @expectedException \Davaxi\Takuzu\InvalidGridException
     */
    public function testSetGridValue_InvalidColumnPosition()
    {
        $this->grid = new Grid_Mockup(2, 4);
        $this->grid->setGridValue(3, 2, Grid::ONE);
    }

    public function testSetGridValue_Equals()
    {
        $this->grid = new Grid_Mockup(2, 4);
        $originalEmptyCount = $this->grid->getEmptyCaseCount();
        $this->grid->setGridValue(0, 0, Grid::UNDEFINED);
        $emptyCount = $this->grid->getEmptyCaseCount();
        $this->assertEquals($originalEmptyCount, $emptyCount);
    }

    public function testSetGridValue_toUndefined()
    {
        $this->grid = new Grid_Mockup();
        $this->grid->setGridFromArray([[1, -1],  [-1, -1]]);
        $originalEmptyCount = $this->grid->getEmptyCaseCount();
        $this->grid->setGridValue(0, 0, Grid::UNDEFINED);
        $emptyCount = $this->grid->getEmptyCaseCount();
        $this->assertEquals($originalEmptyCount + 1, $emptyCount);
        $gridArray = $this->grid->getGridArray();
        $this->assertEquals([[-1, -1], [-1, -1]], $gridArray);
    }

    public function testSetGridValue_toDefined()
    {
        $this->grid = new Grid_Mockup();
        $this->grid->setGridFromArray([[-1, -1],  [-1, -1]]);
        $originalEmptyCount = $this->grid->getEmptyCaseCount();
        $this->grid->setGridValue(0, 0, Grid::ONE);
        $emptyCount = $this->grid->getEmptyCaseCount();
        $this->assertEquals($originalEmptyCount - 1, $emptyCount);
        $gridArray = $this->grid->getGridArray();
        $this->assertEquals([[1, -1], [-1, -1]], $gridArray);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetGridValue_InvalidValue()
    {
        $this->grid = new Grid_Mockup(2, 4);
        $this->grid->setGridValue(0, 1, 'invalid value');
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
    public function testSetGridFromArray_InvalidDimensions()
    {
        $this->grid->setGridFromArray(
            [
                [0, 0],
                [0, 0],
                [1, 1],
                [1, 1, 0, 0]
            ]
        );
    }

    public function testGetLines()
    {
        $grid = [
            [0, 0, 1, 1],
            [0, 1, 0, 1],
        ];
        $this->grid->setGridFromArray($grid);
        $lines = $this->grid->getLines();
        $this->assertEquals($grid, $lines);
    }

    public function testGetGridString()
    {
        $grid = [
            [0, 0, 1, 1],
            [0, 1, 0, 1],
        ];
        $this->grid->setGridFromArray($grid);
        $gridString = $this->grid->getGridString('#');
        $this->assertEquals("0#0#1#1\n0#1#0#1", $gridString);
        $gridString = $this->grid->getGridString(' ');
        $this->assertEquals("0 0 1 1\n0 1 0 1", $gridString);
    }

    public function testGetUnresolvedLines()
    {
        $grid = [
            [0, 0, 1, 1],
            [0, -1, 0, 1],
        ];
        $this->grid->setGridFromArray($grid);
        $lines = $this->grid->getUnresolvedLines();
        $this->assertEquals(
            [1 => [0, -1, 0, 1]],
            $lines
        );
    }

    public function testGetUnresolvedColumns()
    {
        $grid = [
            [0, 0, 1, -1],
            [0, 1, -1, -1],
        ];
        $this->grid->setGridFromArray($grid);
        $lines = $this->grid->getUnresolvedColumns();
        $this->assertEquals(
            [
                2 => [1, -1],
                3 => [-1, -1]
            ],
            $lines
        );
    }

    /**
     * @expectedException LogicException
     */
    public function testFirstEmptyCase_noEmpty()
    {
        $grid = [
            [0, 0, 1, 1],
            [0, 1, 0, 1],
        ];
        $this->grid->setGridFromArray($grid);
        $this->grid->getFirstEmptyCasePosition();
    }

    public function testFirstEmptyCase()
    {
        $grid = [
            [0, -1, 1, 1],
            [0, 1, -1, 1],
        ];
        $this->grid->setGridFromArray($grid);
        $positions = $this->grid->getFirstEmptyCasePosition();
        $this->assertInternalType('array', $positions);
        $this->assertEquals([0, 1], $positions);
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

    public function testToString()
    {
        $grid = [
            [0, 0, 1, 1],
            [0, 1, 0, 1],
        ];
        $this->grid->setGridFromArray($grid);
        $this->assertEquals("0 0 1 1\n0 1 0 1", (string)$this->grid);
    }
}