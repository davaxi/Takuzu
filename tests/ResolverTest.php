<?php

use Davaxi\Takuzu\Grid;
use Davaxi\Takuzu\Resolver;

/**
 * Class Resolver_Mockup
 */
class Resolver_Mockup extends Resolver
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
 * Class ResolverTest
 */
class ResolverTest extends PHPUnit_Framework_TestCase
{

    public function _resolve($originalGridString, $expectedGridString)
    {
        $grid = new Grid();
        $grid->setGridFromString($originalGridString);

        $resolver = new Resolver_Mockup($grid);
        $resolver->resolve();

        $resolvedGrid = $resolver->getResolvedGrid();
        $resolvedGridArray = $resolvedGrid->getGridString();

        $this->assertTrue($resolver->hasResolved());
        $this->assertEquals($expectedGridString, $resolvedGridArray);
    }

    public function testResolveEasy4()
    {
        $this->_resolve(

            ".1.0\n" .
            "..0.\n" .
            ".0..\n" .
            "11.0",

            "0110\n" .
            "1001\n" .
            "0011\n" .
            "1100"
        );
    }

    public function testResolveEasy10()
    {
        $this->_resolve(

            ".0..0...1.\n" .
            "1.........\n" .
            "....0.01.1\n" .
            "1..0.00..0\n" .
            "..0.......\n" .
            ".......1..\n" .
            "0..00.0..1\n" .
            "0.00.1....\n" .
            ".........1\n" .
            ".0...1..1.",

            "1011001010\n" .
            "1100101001\n" .
            "0101010101\n" .
            "1010100110\n" .
            "0101011001\n" .
            "1001101100\n" .
            "0110010011\n" .
            "0100110110\n" .
            "1011001001\n" .
            "0010110110"
        );
    }

    public function testResolveMedium10()
    {
        $this->_resolve(

            "11....10..\n" .
            "...0....1.\n" .
            "..00.....0\n" .
            ".....00..1\n" .
            "..........\n" .
            "..........\n" .
            "1..10.....\n" .
            "1.....00..\n" .
            ".0....0...\n" .
            "..11....10",

            "1101001001\n" .
            "0010011011\n" .
            "0100110110\n" .
            "1001100101\n" .
            "0110011010\n" .
            "0101100101\n" .
            "1011001100\n" .
            "1100110010\n" .
            "0010110101\n" .
            "1011001010"
        );
    }

    public function testResolveMedium10_2()
    {
        $this->_resolve(

            "1..1...0..\n" .
            "..0.1....1\n" .
            "......0.01\n" .
            "..00......\n" .
            ".....1..0.\n" .
            ".....1.1..\n" .
            "..0.0...0.\n" .
            "1.....00.0\n" .
            "..1.0.0.0.\n" .
            "0.1.1...01",

            "1011001010\n" .
            "0100101011\n" .
            "0011010101\n" .
            "1100100110\n" .
            "1001011001\n" .
            "0010110110\n" .
            "0101001101\n" .
            "1100110010\n" .
            "1011010100\n" .
            "0110101001"
        );
    }

    public function testResolveHard10()
    {
        $this->_resolve(

            "1...10....\n" .
            "...0....0.\n" .
            "....1...00\n" .
            ".1.....1..\n" .
            "..........\n" .
            "1..0.0..00\n" .
            ".......1..\n" .
            "..0.......\n" .
            "..........\n" .
            "1.........",

            "1001101010\n" .
            "0110011001\n" .
            "1010110100\n" .
            "0101100110\n" .
            "0010011011\n" .
            "1100101100\n" .
            "0011010101\n" .
            "1101001010\n" .
            "0110100101\n" .
            "1001010011"
        );
    }

    public function testResolveEvil10()
    {
        $this->_resolve(

            "..1.11....\n" .
            "..........\n" .
            "11..11.1..\n" .
            "1...11....\n" .
            ".......0..\n" .
            "...0.....0\n" .
            "11........\n" .
            "1.........\n" .
            "...0....0.\n" .
            "..1.......",

            "0010110011\n" .
            "0101001011\n" .
            "1100110100\n" .
            "1010110100\n" .
            "0011001011\n" .
            "0100110110\n" .
            "1101001001\n" .
            "1011010010\n" .
            "0100101101\n" .
            "1011001100"
        );
    }

    public function testResolveEasy14()
    {
        $this->_resolve(

            "..0.1..0...0..\n" .
            ".0.....0..0.1.\n" .
            "...0.1........\n" .
            "......0..00.0.\n" .
            "1.11...1.....1\n" .
            "0..1.0..0.0...\n" .
            ".1.....0..10..\n" .
            "..00..1.....0.\n" .
            "...1.0..1.1..0\n" .
            "1.....0...01.0\n" .
            ".1.00..0......\n" .
            "........1.1...\n" .
            ".0.1..0.....0.\n" .
            "..0...0..0.1..",

            "10011010101001\n" .
            "00110010110110\n" .
            "01100101011010\n" .
            "11001100100101\n" .
            "10110011001001\n" .
            "00110011010110\n" .
            "11001100101010\n" .
            "01001011010101\n" .
            "10110010101010\n" .
            "11001101010100\n" .
            "01100100110011\n" .
            "10011010101010\n" .
            "00110101010101\n" .
            "01001101001101"
        );
    }

}