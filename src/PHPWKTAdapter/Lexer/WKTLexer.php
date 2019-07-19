<?php

namespace daandesmedt\PHPWKTAdapter\Lexer;

use Doctrine\Common\Lexer\AbstractLexer;


class WKTLexer extends AbstractLexer
{
    // character types
    const T_NONE = 1;
    const T_INTEGER = 2;
    const T_STRING = 3;
    const T_FLOAT = 5;
    const T_CLOSE_PARENTHESIS = 6;
    const T_OPEN_PARENTHESIS = 7;
    const T_COMMA = 8;
    const T_DOT = 10;
    const T_EQUALS = 11;
    const T_SEMICOLON = 50;

    // SRID 
    const T_SRID = 500;
    const T_Z = 501;
    const T_M = 502;
    const T_ZM = 501;

    // Geometry types
    const T_TYPE = 600;
    const T_POINT = 601;
    const T_LINESTRING = 602;
    const T_POLYGON = 603;
    const T_MULTIPOINT = 604;
    const T_MULTILINESTRING = 605;
    const T_MULTIPOLYGON = 606;
    const T_GEOMETRYCOLLECTION = 607;

    /**
     * getCatchablePatterns
     * 
     * @return array
     */
    protected function getCatchablePatterns()
    {
        return array(
            '',
            'zm|[a-z]+[a-ln-y]',
            '[+-]?[0-9]+(?:[\.][0-9]+)?(?:e[+-]?[0-9]+)?'
        );
    }


    /**
     * getNonCatchablePatterns
     * 
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        return array('\s+');
    }


    /**
     * getType
     *
     * @param string $value
     * @return int
     */
    protected function getType(&$value) : int
    {
        // check numeric
        if (is_numeric($value)) {
            $value += 0;
            if (is_int($value)) {
                return self::T_INTEGER;
            }
            return self::T_FLOAT;
        }

        // check geom / srid type
        if (ctype_alpha($value)) {
            $name = __class__ . '::T_' . strtoupper($value);
            if (defined($name)) {
                return constant($name);
            }
            return self::T_STRING;
        }

        // check characteral type
        switch ($value) {
            case '.':
                return self::T_DOT;
            case ',':
                return self::T_COMMA;
            case '(':
                return self::T_OPEN_PARENTHESIS;
            case ')':
                return self::T_CLOSE_PARENTHESIS;
            case '=':
                return self::T_EQUALS;
            case ';':
                return self::T_SEMICOLON;
            default:
                return self::T_NONE;
        }
    }

    /**
     * getValue
     * 
     * @return string
     */
    public function getValue() : string
    {
        return $this->token['value'];
    }

    /**
     * getLookaheadType
     * 
     * @return int
     */
    public function getLookaheadType() : int
    {
        return (int)$this->lookahead['type'];
    }

}