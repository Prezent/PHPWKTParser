<?php

namespace daandesmedt\PHPWKTAdapter;

use daandesmedt\PHPWKTAdapter\Lexer\WKTLexer;
use UnexpectedValueException;

class WKTAdapter
{

    private $wkt;
    private $type;
    private $srid;
    private $dimension;

    private $lexer;


    /**
     * Get lexer
     *
     * @return WKTLexer
     */
    public function getLexer() : WKTLexer
    {
        $this->lexer = $this->lexer ?? new WKTLexer();
        return $this->lexer;
    }


    /**
     * Set lexer
     *
     * @param WKTLexer $lexer
     */
    public function setLexer(WKTLexer $lexer) : self
    {
        $this->lexer = $lexer;
        return $this;
    }


    /**
     * Read WKT
     *
     * @param string|null $wkt
     * @return array
     */
    public function read($wkt)
    {
        $this->reset();
        $this->wkt = $wkt;

        $this->getLexer()->setInput($this->wkt);
        $this->getLexer()->moveNext();

        if ($this->getLexer()->isNextToken(WKTLexer::T_SRID)) {
            $this->srid = $this->getSRID();
        }

        $geometry = $this->getGeometry();
        $geometry['srid'] = $this->srid;
        $geometry['dimension'] = $this->dimension;

        return $geometry;
    }


    /**
     * Reset
     */
    private function reset()
    {
        $this->srid = null;
        $this->type = null;
        $this->dimension = null;
        $this->wkt = null;
    }


    /**
     * Parse spatial geometry object
     *
     * @return array
     */
    protected function getGeometry()
    {
        $type = $this->getType();
        $this->type = $type;

        if ($this->getLexer()->isNextTokenAny(array(WKTLexer::T_Z, WKTLexer::T_M, WKTLexer::T_ZM))) {
            $this->match($this->getLexer()->getLookaheadType());
            $this->dimension = $this->getLexer()->getValue();
        }

        $this->match(WKTLexer::T_OPEN_PARENTHESIS);
        $values = $this->$type();
        $this->match(WKTLexer::T_CLOSE_PARENTHESIS);

        return array(
            'type' => $type,
            'value' => $values
        );
    }


    /**
     * Match lexer geo T_TYPE
     * 
     * @return string
     */
    private function getType()
    {
        $this->match(WKTLexer::T_TYPE);
        return $this->getLexer()->getValue();
    }


    /**
     * Point parser
     * 
     * @return array
     */
    private function point()
    {
        if (null !== $this->dimension) {
            return $this->getCoordinates(2 + strlen($this->dimension));
        }

        $values = $this->getCoordinates(2);

        for ($i = 3; $i <= 4 && $this->lexer->isNextTokenAny(array(WKTLexer::T_FLOAT, WKTLexer::T_INTEGER)); $i++) {
            $values[] = $this->getCoordinate();
        }

        switch (count($values)) {
            case 2:
                $this->dimension = null;
                break;
            case 3:
                $this->dimension = 'Z';
                break;
            case 4:
                $this->dimension = 'ZM';
                break;
        }

        return $values;
    }


    /**
     * Polygon spatial data type parser
     * 
     * @return array
     */
    private function polygon()
    {
        return $this->parsePointsCollection();
    }


    /**
     * Linestring spatial data type parser
     * 
     * @return array
     */
    private function linestring()
    {
        return $this->parsePoints();
    }


    /**
     * Multilinestring spatial data type parser
     * 
     * @return array
     */
    private function multilinestring()
    {
        return $this->parsePointsCollection();
    }


    /**
     * Multipoint spatial data type parser
     * 
     * @return array
     */
    private function multipoint()
    {
        return $this->parsePoints();
    }


    /**
     * Multipolygon spatial data type parser
     * 
     * @return array
     */
    private function multipolygon()
    {
        $this->match(WKTLexer::T_OPEN_PARENTHESIS);
        $polygons = array($this->polygon());
        $this->match(WKTLexer::T_CLOSE_PARENTHESIS);
        while ($this->getLexer()->isNextToken(WKTLexer::T_COMMA)) {
            $this->match(WKTLexer::T_COMMA);
            $this->match(WKTLexer::T_OPEN_PARENTHESIS);
            $polygons[] = $this->polygon();
            $this->match(WKTLexer::T_CLOSE_PARENTHESIS);
        }
        return $polygons;
    }


    /**
     * GeometryCollection spatial data type parser
     * 
     * @return array
     */
    private function geometrycollection()
    {
        $collection = array($this->getGeometry());
        while ($this->lexer->isNextToken(WKTLexer::T_COMMA)) {
            $this->match(WKTLexer::T_COMMA);
            $collection[] = $this->getGeometry();
        }
        return $collection;
    }


    /**
     * CircularString spatial data type parser
     * 
     * @return array
     */
    private function circularstring()
    {
        return $this->parsePoints();
    }


    /**
     * Parse point collection
     * 
     * @return array
     */
    private function parsePointsCollection()
    {
        $this->match(WKTLexer::T_OPEN_PARENTHESIS);
        $points = $this->parsePoints();
        $this->match(WKTLexer::T_CLOSE_PARENTHESIS);
        while ($this->getLexer()->isNextToken(WKTLexer::T_COMMA)) {
            $this->match(WKTLexer::T_COMMA);
            $this->match(WKTLexer::T_OPEN_PARENTHESIS);
            $points[] = $this->parsePoints();
            $this->match(WKTLexer::T_CLOSE_PARENTHESIS);
        }
        return $points;
    }


    /**
     * Parse points
     * 
     * @return array
     */
    private function parsePoints()
    {
        $points = array($this->point());
        while ($this->getLexer()->isNextToken(WKTLexer::T_COMMA)) {
            $this->match(WKTLexer::T_COMMA);
            $points[] = $this->point();
        }
        return $points;
    }


    /**
     * Get coordinates
     * 
     * @return array
     */
    private function getCoordinates($count)
    {
        $values = array();
        for ($i = 1; $i <= $count; $i++) {
            $values[] = $this->getCoordinate();
        }
        return $values;
    }


    /**
     * Get coordinate
     * 
     * @return int|float
     */
    private function getCoordinate()
    {
        $this->match(($this->getLexer()->isNextToken(WKTLexer::T_FLOAT)) ? WKTLexer::T_FLOAT : WKTLexer::T_INTEGER);
        return $this->getLexer()->getValue();
    }


    /**
     * Get SRID
     * 
     * @return string
     */
    private function getSRID() : string
    {
        $this->match(WKTLexer::T_SRID);
        $this->match(WKTLexer::T_EQUALS);
        $this->match(WKTLexer::T_INTEGER);
        $srid = $this->getLexer()->getValue();
        $this->match(WKTLexer::T_SEMICOLON);
        return $srid;
    }


    /**
     * Match WKT token
     * 
     * @throws 
     */
    private function match($token)
    {
        $lookaheadType = $this->getLexer()->getLookaheadType();
        if ($lookaheadType !== $token && ($token !== WKTLexer::T_TYPE || $lookaheadType <= WKTLexer::T_TYPE)) {
            throw $this->doUnexpectedValue($this->getLexer()->getLiteral($token));
        }
        $this->getLexer()->moveNext();
    }


    /**
     * Unexpected value exception
     * 
     * @throws UnexpectedValueException
     */
    private function doUnexpectedValue($expected)
    {
        $expected = sprintf('Expected %s, got', $expected);
        $token = $this->getLexer()->lookahead;
        $found = null === $this->getLexer()->lookahead ? 'end of string.' : sprintf('"%s"', $token['value']);
        $message = sprintf(
            'Unexpected value exception at line 0, col %d: Error: %s %s in value "%s"',
            isset($token['position']) ? $token['position'] : '-1',
            $expected,
            $found,
            $this->wkt
        );
        return new UnexpectedValueException($message);
    }

}