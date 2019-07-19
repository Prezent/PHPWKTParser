PHPWKTParser
===============

Well-known text (WKT) is a text markup language for representing vector geometry objects on a map, spatial reference systems of spatial objects and transformations between spatial reference systems.


EWKT (Extended Well-Known Text), a PostGIS-specific format that includes the spatial reference system identifier (SRID) and up to 4 ordinate values (XYZM), is also supported (ex: `SRID=31370;POINT(44 60)`).


`PHPWKTParser` provides a simple usage helper class to read WKT and EWKT and parse this text representation to a workable PHP array holding the parsed WKT/EWKT definition. Read and parse 2D, 3D and 4D WKT (Well Known Text) / EWKT (Extended Well-Known Text) object strings into geometry objects with this simple WKT PHP adapter library.



## Installation

Install the package through [composer](http://getcomposer.org):

```
composer require daandesmedt/phpwktadapter
```

Make sure, that you include the composer [autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading) somewhere in your codebase.


## Supported geometry 

| Geometry Type	|   Example     |
| --- | --- |
| POINT         | POINT(30 10) |
| LNESTRING     | LINESTRING(30 10, 10 30, 40 40) |
| POLYGON | POLYGON((0 0,10 0,10 10,0 10,0 0)) |
| MULTIPOINT | MULTIPOINTZM(0 0 10 10,10 0 0 0,10 10 0 0,20 20 0 10) |
| MULTILINESTRING | MULTILINESTRING((0 0,10 0,10 10,0 10)) |
| MULTIPOLYGON | MULTIPOLYGON(((40 40, 20 45, 45 30, 40 40)), ((20 35, 10 30, 10 10, 30 5, 45 20, 20 35), (30 20, 20 15, 20 25, 30 20))) |
| GEOMETRYCOLLECTION | GEOMETRYCOLLECTION(POINT(10 20),LINESTRING(0 0,10 0)) |


## PHPWKTParser parsed and returned geometry array response

The `read($wkt)` function of the `PHPWKTParser` adapter will return a associative array as representation of the parsed WKT/EWKT (in case of valid).


```
array(
    // the geometry object type
    "type"      =>  string,
    // integer or float values for POINT - nested array (integer or float) for other geometry types
    "value"     =>  array,
    // integer representing the EWKT SRID, null when not present 
    "srid"      =>  integer | null,
    // string (Z, M or ZM) representing the dimension, null when not present 
    "dimension" =>  string | null
)
```

## Working examples

Working examples can be found in the `examples` folder.


## Sample usage

```php
<?php 

require __DIR__ . '/../vendor/autoload.php';

use daandesmedt\PHPWKTAdapter\WKTAdapter;

$adapter = new WKTAdapter();
$res = $adapter->read('SRID=31370;POINT(30 10)');
var_dump($res); 
```

## Handling exceptions

Invalid format in the specified WKT / EWKT will result in a `UnexpectedValueException` thrown by the `WKTAdapter` .


```php
<?php 

require __DIR__ . '/../vendor/autoload.php';

use daandesmedt\PHPWKTAdapter\WKTAdapter;

$adapter = new WKTAdapter();

try {
    $res = $adapter->read('SRID=31370;POINT(bad format)');
} catch (UnexpectedValueException $e) {
    var_dump($e->getMessage());
}
```