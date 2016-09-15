# Takuzu

PHP Class to check and resolve by step (with explain) Takuzu grids.

[![Build Status](https://travis-ci.org/davaxi/Takuzu.svg)](https://travis-ci.org/davaxi/Takuzu)
[![Latest Stable Version](https://poser.pugx.org/davaxi/takuzu/v/stable)](https://packagist.org/packages/davaxi/takuzu) 
[![Total Downloads](https://poser.pugx.org/davaxi/takuzu/downloads)](https://packagist.org/packages/davaxi/takuzu) 
[![Latest Unstable Version](https://poser.pugx.org/davaxi/takuzu/v/unstable)](https://packagist.org/packages/davaxi/takuzu) 
[![License](https://poser.pugx.org/davaxi/takuzu/license)](https://packagist.org/packages/davaxi/takuzu)
[![Code Climate](https://codeclimate.com/github/davaxi/Takuzu/badges/gpa.svg)](https://codeclimate.com/github/davaxi/Takuzu)
[![Test Coverage](https://codeclimate.com/github/davaxi/Takuzu/badges/coverage.svg)](https://codeclimate.com/github/davaxi/Takuzu/coverage)
[![Issue Count](https://codeclimate.com/github/davaxi/Takuzu/badges/issue_count.svg)](https://codeclimate.com/github/davaxi/Takuzu)

## Game 

Takuzu is a logic-based number placement puzzle. The objective is to 
fill a (usually 10×10) grid with 1s and 0s, where there is an equal 
number of 1s and 0s in each row and column and no more than two of 
either number adjacent to each other. Additionally, there can be no 
identical rows or columns. 

Source: [Wikipédia](https://en.wikipedia.org/wiki/Takuzu)

## Solving Methods

- Each row and each column should contain an equal number of 0s and 1s. 
If the required number of 0s or 1s is reached in a row or a column, 
the remaining cells should contain the other number. (1xx101 - 100101)

- More than two of the same number can't be adjacent. If two adjacent 
cells contain the same number, the cells next to the numbers should 
contain the other number. (xxx00x - xx1001) If two cells contain the 
same number with an empty cell between, this empty cell should contain 
the other number because otherwise three same number appears. 
(x1x1xx - x101xx)

- Each row and column is unique. (100101 1001xx - 100101 100110)

- Eliminate impossible combinations. For example, 110xxx, the cell n°6 
should contain 0 because otherwise a trio appears (110xx1 - 110001)

## Installation

This page contains information about installing the Library for PHP.

### Requirements

- PHP version 5.3.0 or greater

### Obtaining the client library

There are two options for obtaining the files for the client library.

#### Using Composer

You can install the library by adding it as a dependency to your composer.json.

```
  "require": {
    "davaxi/takuzu": "^1.0"
  }
```

#### Cloning from GitHub

The library is available on [GitHub](https://github.com/davaxi/Takuzu). You can clone it into a local repository with the git clone command.

```
git clone https://github.com/davaxi/Takuzu.git
```

### What to do with the files

After obtaining the files, ensure they are available to your code. If you're using Composer, this is handled for you automatically. If not, you will need to add the `autoload.php` file inside the client library.

```
require '/path/to/takuzu/folder/autoload.php';
```

## Examples

```php
<?php

// ...

$grid = new \Davaxi\Takuzu\Grid();
$grid->setGridFromString(
    "1..1...0..\n" .
    "..0.1....1\n" .
    "......0.01\n" .
    "..00......\n" .
    ".....1..0.\n" .
    ".....1.1..\n" .
    "..0.0...0.\n" .
    "1.....00.0\n" .
    "..1.0.0.0.\n" .
    "0.1.1...01"
);

$resolver = new \Davaxi\Takuzu\Resolver($grid);
$resolver->resolve();

$resolvedGrid = $resolver->getResolvedGrid();
```