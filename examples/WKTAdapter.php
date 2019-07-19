<?php 

require __DIR__ . '/../vendor/autoload.php';

use daandesmedt\PHPWKTAdapter\WKTAdapter;


$adapter = new WKTAdapter();


// HANDLE EXCEPTION
// #########################################################
try {
    $res = $adapter->read('SRID=31370;POINT(bad format)');
} catch (UnexpectedValueException $e) {
    var_dump($e->getMessage());
}


// POINT
// #########################################################
$res = $adapter->read('SRID=31370;POINT(30 10)');
$res = $adapter->read('SRID=31370;POINTZM(30 10 5 60)');
$res = $adapter->read('SRID=31370;POINT ZM (30 10 5 60)');
$res = $adapter->read('SRID=31370;POINTM(30 10 80)');
$res = $adapter->read('SRID=31370;POINT M (30 10 80)');
$res = $adapter->read('SRID=31370;POINT Z (30 10 80)');


// POLYGON
// #########################################################
$res = $adapter->read('SRID=31370;POLYGON((0 0,10 0,10 10,0 10,0 0))');
var_dump($res);
exit;

$res = $adapter->read('SRID=31370;POLYGON((0 0,10 0,10 10,0 10,0 0))');
$res = $adapter->read('SRID=31370;POLYGONZM((0 0 0 1,10 0 0 1,10 10 0 1,0 10 0 1,0 0 0 1))');
$res = $adapter->read('SRID=31370;POLYGONM((0 0 1,10 0 1,10 0 1,0 0 1,0 0 1))');


// LINESTRING
// #########################################################
$res = $adapter->read('SRID=31370;LINESTRING(30 10, 10 30, 40 40)');
$res = $adapter->read('SRID=31370;LINESTRINGZM(15 15 0 0, 20 20 0 0)');
$res = $adapter->read('SRID=31370;LINESTRINGM(15 15 0, 20 20 0)');


// MULTIPOINT
// #########################################################
$res = $adapter->read('SRID=31370;MULTIPOINT(0 10,10 10,10 20,10 30)');
$res = $adapter->read('SRID=31370;MULTIPOINTZM(0 0 10 10,10 0 0 0,10 10 0 0,20 20 0 10)');
$res = $adapter->read('SRID=31370;MULTIPOINTM(0 10 10,10 10 10,0 0 0,20 10 30)');


// MULTILINESTRING 
// #########################################################
$res = $adapter->read('SRID=31370;MULTILINESTRING((0 0,10 0,10 10,0 10))');
$res = $adapter->read('SRID=31370;MULTILINESTRING((0 0,10 0,10 10,0 10),(5 5,7 5,7 7,5 7))');
$res = $adapter->read('SRID=31370;MULTILINESTRINGZM((0 0 10 10,10 0 0 0,10 10 0 0,20 20 0 10),(60 40 10 10,70 10 20 40))');
$res = $adapter->read('SRID=31370;MULTILINESTRINGM((0 10 10,10 10 10,0 0 0,20 10 30))');


// MULTIPOLYGON
// #########################################################
$res = $adapter->read('SRID=31370;MULTIPOLYGON (((30 20, 45 40, 10 40, 30 20)),((15 5, 40 10, 10 20, 5 10, 15 5)))');
$res = $adapter->read('SRID=31370;MULTIPOLYGON (((40 40, 20 45, 45 30, 40 40)), ((20 35, 10 30, 10 10, 30 5, 45 20, 20 35), (30 20, 20 15, 20 25, 30 20)))');
$res = $adapter->read('SRID=31370;MULTIPOLYGONZM(((40 40 10 10, 20 45 10 10, 45 30 10 10, 40 40 10 10)), ((20 35 10 10, 10 30 10 10, 10 10 10 10, 30 5 10 10, 45 20 10 10, 20 35 10 10)))');
$res = $adapter->read('SRID=31370;MULTIPOLYGONM(((40 40 10, 20 45 10, 45 30 10, 40 40 10)), ((20 35 10, 10 30 10, 10 10 10, 30 5 10, 45 20 10, 20 35 10)))');


// GEOMETRYCOLLECTION
// #########################################################
$res = $adapter->read('SRID=31370;GEOMETRYCOLLECTION(POINT(10 20),LINESTRING(0 0,10 0))');
$res = $adapter->read('GEOMETRYCOLLECTION(POINT(10 20),POINT(0 10))');
$res = $adapter->read('GEOMETRYCOLLECTIONM(POINT(10 20 0),POINT(10 0 10))');
$res = $adapter->read('GEOMETRYCOLLECTIONZM(POINT(10 20 0 0),POINT(10 0 0 10))');


// DUMP
// #########################################################
var_dump($res);