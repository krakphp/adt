# ADT (Algebraic Data Types)

Poor man's implementation of [Algebraic Data Types](https://en.wikipedia.org/wiki/Algebraic_data_type) in PHP.

Also known as an enum with an associated value in other languages like Swift or Rust.

## Installation

Install with composer at `krak/adt`

## Usage

```php
<?php

use Krak\ADT\ADT;

/**
 * @method static Upc upc(int $numberSystem, int $manufacturer, int $product, int $check)
 * @method static QrCode qrCode(string $productCode)
 */
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

// static constructors
$qrCode = Barcode::qrCode('abc123');
```

### Autoloading Concerns

With the example above, if you try to create a QrCode or Upc before ever referencing the Barcode class, you'll likely get a file not found when using composer's psr-4 autoloader.

You can get around this a few ways:

1. Utilize class mapping
2. Include the enum class file in the composer autoload.files section (something like [php-inc](https://github.com/krakphp/php-inc) could make that easier)
3. Utilize static constructors provided by the base ADT class:

    ```php
    <?php

    $upc = Barcode::upc(1, 2, 3, 4);
    $qrCode = Barcode::qrCode('abc123');
    ```

## Tests

Tests are run via `make test` and are stored in the `test` directory. We use peridot for testing.
