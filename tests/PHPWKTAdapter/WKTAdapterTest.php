<?php

declare (strict_types = 1);

namespace daandesmedt\Tests\PHPWKTAdapter;

use daandesmedt\PHPWKTAdapter\WKTAdapter;
use daandesmedt\PHPWKTAdapter\UnexpectedValueException;
use daandesmedt\PHPWKTAdapter\Lexer\WKTLexer;

use PHPUnit\Framework\TestCase;

final class WKTAdapterTest extends TestCase
{

    // LEXER
    public function testGetSetLexer()
    {
        $adapter = new WKTAdapter();
        $this->assertInstanceOf(WKTLexer::class, $adapter->getLexer());
        $lexer = new WKTLexer();
        $this->assertEquals($lexer, $adapter->setLexer($lexer)->getLexer());
    }

    // FORMAT TESTS
    public function testParsingIncorrectValue()
    {
        $this->doTest(
            array(
                'read' => 'blablabla',
                'expectedException' => 'UnexpectedValueException'
            )
        );
    }

    public function testParsingIncorrect()
    {
        $this->doTest(
            array(
                'read' => 'POINTT(30 10)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
    }


    // DIMENSION
    public function testDimensionDeclaredZM()
    {
        $this->doTest(
            array(
                'read' => 'POINTZM(30 10 5 60)',
                'expected' => array(
                    'srid' => null,
                    'type' => 'POINT',
                    'value' => array(30, 10, 5, 60),
                    'dimension' => 'ZM'
                )
            )
        );
    }

    public function testDimensionDeclaredM()
    {
        $this->doTest(
            array(
                'read' => 'POINTM(30 10 5)',
                'expected' => array(
                    'srid' => null,
                    'type' => 'POINT',
                    'value' => array(30, 10, 5),
                    'dimension' => 'M'
                )
            )
        );
    }


    public function testDimensionDeclaredZ()
    {
        $this->doTest(
            array(
                'read' => 'POINTZ(30 10 5)',
                'expected' => array(
                    'srid' => null,
                    'type' => 'POINT',
                    'value' => array(30, 10, 5),
                    'dimension' => 'Z'
                )
            )
        );
    }

    public function testUnknownDimensionDeclared()
    {
        $this->doTest(
            array(
                'read' => 'POINTP(30 10 5)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
    }



    // SRID
    public function testWithSRID()
    {
        $this->doTest(
            array(
                'read' => 'SRID=31370;POINT(30 10)',
                'expected' => array(
                    'srid' => 31370,
                    'type' => 'POINT',
                    'value' => array(30, 10),
                    'dimension' => null
                )
            )
        );
    }

    public function testWithFaultySRID()
    {
        $this->doTest(
            array(
                'read' => 'SRID=3137A0;POINT(30 10)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
        $this->doTest(
            array(
                'read' => 'SRIDa=31370;POINT(30 10)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
    }


    // POINT
    public function testPoint()
    {
        $this->doTest(
            array(
                'read' => 'POINT(30 10)',
                'expected' => array(
                    'srid' => null,
                    'type' => 'POINT',
                    'value' => array(30, 10),
                    'dimension' => null
                )
            )
        );
    }

    public function testPointWithIncorrectCoordinatesFormat()
    {
        $this->doTest(
            array(
                'read' => 'POINT(30, 10 10)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
        $this->doTest(
            array(
                'read' => 'POINT(30,)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
        $this->doTest(
            array(
                'read' => 'POINT(,)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
    }

    public function testPointWithIncorrectCoordinates()
    {
        $this->doTest(
            array(
                'read' => 'POINT(30 10 10)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
        $this->doTest(
            array(
                'read' => 'POINT(30)',
                'expectedException' => 'UnexpectedValueException'
            )
        );
        $this->doTest(
            array(
                'read' => 'POINT()',
                'expectedException' => 'UnexpectedValueException'
            )
        );
    }

    // POLYGON
    public function testPolygon()
    {
        $this->doTest(
            array(
                'read' => 'POLYGON((0 0,30 40,10 20,0 10,0 0))',
                'expected' => array(
                    'srid' => null,
                    'type' => 'POLYGON',
                    'value' => array(
                        array(0, 0),
                        array(30, 40),
                        array(10, 20),
                        array(0, 10),
                        array(0, 0)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    public function testPolygonWithSRID()
    {
        $this->doTest(
            array(
                'read' => 'SRID=31370;POLYGON((0 0,30 40,10 20,0 10,0 0))',
                'expected' => array(
                    'srid' => 31370,
                    'type' => 'POLYGON',
                    'value' => array(
                        array(0, 0),
                        array(30, 40),
                        array(10, 20),
                        array(0, 10),
                        array(0, 0)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    // LINESTRING
    public function testLineString()
    {
        $this->doTest(
            array(
                'read' => 'LINESTRING(30 10, 10 30, 40 40)',
                'expected' => array(
                    'srid' => null,
                    'type' => 'LINESTRING',
                    'value' => array(
                        array(30, 10),
                        array(10, 30),
                        array(40, 40)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    public function testLineStringWithSRID()
    {
        $this->doTest(
            array(
                'read' => 'SRID=31370;LINESTRING(30 10, 10 30, 40 40)',
                'expected' => array(
                    'srid' => 31370,
                    'type' => 'LINESTRING',
                    'value' => array(
                        array(30, 10),
                        array(10, 30),
                        array(40, 40)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    // MULTIPOINT
    public function testMultiPoint()
    {
        $this->doTest(
            array(
                'read' => 'MULTIPOINT(0 10,10 10,10 20,10 30)',
                'expected' => array(
                    'srid' => null,
                    'type' => 'MULTIPOINT',
                    'value' => array(
                        array(0, 10),
                        array(10, 10),
                        array(10, 20),
                        array(10, 30)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    public function testMultiPointWithSRID()
    {
        $this->doTest(
            array(
                'read' => 'SRID=31370;MULTIPOINT(0 10,10 10,10 20,10 30)',
                'expected' => array(
                    'srid' => 31370,
                    'type' => 'MULTIPOINT',
                    'value' => array(
                        array(0, 10),
                        array(10, 10),
                        array(10, 20),
                        array(10, 30)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    // MULTILINESTRING
    public function testMultiLinestring()
    {
        $this->doTest(
            array(
                'read' => 'MULTILINESTRING((0 0,10 0,10 10,0 10))',
                'expected' => array(
                    'srid' => null,
                    'type' => 'MULTILINESTRING',
                    'value' => array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    public function testMultiLinestringWithSRID()
    {
        $this->doTest(
            array(
                'read' => 'SRID=31370;MULTILINESTRING((0 0,10 0,10 10,0 10))',
                'expected' => array(
                    'srid' => 31370,
                    'type' => 'MULTILINESTRING',
                    'value' => array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10)
                    ),
                    'dimension' => null
                )
            )
        );
    }


    // MULTIPOLYGON
    public function testMultiPolygon()
    {
        $this->doTest(
            array(
                'read' => 'MULTIPOLYGON (((30 20, 45 40, 10 40, 30 20)),((15 5, 40 10, 10 20, 5 10, 15 5)))',
                'expected' => array(
                    'srid' => null,
                    'type' => 'MULTIPOLYGON',
                    'value' => array(
                        array(
                            array(30, 20),
                            array(45, 40),
                            array(10, 40),
                            array(30, 20)
                        ),
                        array(
                            array(15, 5),
                            array(40, 10),
                            array(10, 20),
                            array(5, 10),
                            array(15, 5)
                        )
                    ),
                    'dimension' => null
                )
            )
        );
    }


    public function testMultilinestringSRID()
    {
        $this->doTest(
            array(
                'read' => 'SRID=31370;MULTIPOLYGON (((30 20, 45 40, 10 40, 30 20)),((15 5, 40 10, 10 20, 5 10, 15 5)))',
                'expected' => array(
                    'srid' => 31370,
                    'type' => 'MULTIPOLYGON',
                    'value' => array(
                        array(
                            array(30, 20),
                            array(45, 40),
                            array(10, 40),
                            array(30, 20)
                        ),
                        array(
                            array(15, 5),
                            array(40, 10),
                            array(10, 20),
                            array(5, 10),
                            array(15, 5)
                        )
                    ),
                    'dimension' => null
                )
            )
        );
    }


    // GEOMETRYCOLLECTION
    public function testGeometryCollection()
    {
        $this->doTest(
            array(
                'read' => 'SRID=31370;GEOMETRYCOLLECTION(POINT(10 20),LINESTRING(30 10, 10 30, 40 40))',
                'expected' => array(
                    'srid' => 31370,
                    'type' => 'GEOMETRYCOLLECTION',
                    'value' => array(
                        array(
                            'type' => 'POINT',
                            'value' => array(10, 20)
                        ),
                        array(
                            'type' => 'LINESTRING',
                            'value' => array(
                                array(30, 10),
                                array(10, 30),
                                array(40, 40)
                            )
                        )
                    ),
                    'dimension' => null
                )
            )
        );
    }

    private function doTest($test)
    {
        $adapter = new WKTAdapter();
        if (isset($test['expectedException'])) {
            $this->expectException($test['expectedException']);
        }
        $response = $adapter->read($test['read']);
        if (!isset($test['expectedException'])) {
            $this->assertEquals($test['expected'], $response);
        }
    }

}