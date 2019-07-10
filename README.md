# ADT (Algebraic Data Types)

Poor man's implementation of [Algebraic Data Types](https://en.wikipedia.org/wiki/Algebraic_data_type) in PHP.

Also known as an enum with an associated value in other languages like Swift or Rust.

## Installation

Install with composer at `krak/adt`

## Usage

```php
<?php

use Krak\ADT\ADT;

abstract class Barcode extends ADT {
    public static function types(): array {
        return [Upc::class, QrCode::class];
    }
}

final class Upc extends Barcode {
    public $numberSystem;
    public $manufacturer;
    public $product;
    public $check;

    public function __construct(int $numberSystem, int $manufacturer, int $product, int $check) {
        $this->numberSystem = $numberSystem;
        $this->manufacturer = $manufacturer;
        $this->product = $product;
        $this->check = $check;
    }
}

final class QrCode extends Barcode {
    public $productCode;

    public function __construct(string $productCode) {
        $this->productCode = $productCode;
    }
}

$barcode = new QrCode('abc123');

// requires that all cases are set or exception is thrown
$oneOrTwo = $barcode->match([
    Upc::class => function(Upc $upc) { return 1;},
    QrCode::class => function(QrCode $qrCode) { return 2; },
]);

// allow a default value
$oneOrNull = $barcode->matchWithDefault([
    Upc::class => function(Upc $upc) { return 1; }
]);

// return static values
$threeOrFour = $barcode->match([
    Upc::class => 3,
    QrCode::class => 4,
]);
```

## Tests

Tests are run via `make test` and are stored in the `test` directory. We use peridot for testing.
