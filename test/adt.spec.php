<?php

namespace Krak\ADT;

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

abstract class Direction extends ADT {
    public static function types(): array {
        return [North::class, South::class, East::class, West::class];
    }
}

final class North extends Direction {}
final class South extends Direction {}
final class East extends Direction {}
final class West extends Direction {}

final class NotRegistered extends Barcode {}

describe('ADT', function() {
    it('can match on class name and invoke callable', function() {
        $barcode = new QrCode('foo');
        $res = $barcode->match([
            Upc::class => function(Upc $upc) {},
            QrCode::class => function(QrCode $qrCode) { return $qrCode->productCode; },
        ]);
        expect($res)->equal('foo');
    });
    it('can match on class name and return raw values', function() {
        $barcode = new Upc(1, 2, 3, 4);
        $res = $barcode->match([
            Upc::class => true,
            QrCode::class => false,
        ]);
        expect($res)->equal(true);
    });
    it('asserts all cases are provided when matching', function() {
        expect(function() {
            $barcode = new QrCode('foo');
            $res = $barcode->match([
                QrCode::class => 1,
            ]);
        })->throw(\RuntimeException::class, 'Case Krak\\ADT\\Upc is not handled in this match statement.');
    });
    it('asserts that the type matched is registered in the adt\'s types', function() {
       expect(function() {
            $barcode = new NotRegistered();
            $res = $barcode->match([
                Upc::class => 1,
                QrCode::class => 2,
            ]);
        })->throw(\RuntimeException::class, 'Type Krak\\ADT\\NotRegistered is not registered in the list of valid types for this ADT.');
    });
    it('can match with a default', function() {
        $barcode = new QrCode('foo');
        $res = $barcode->matchWithDefault([
            Upc::class => 1,
        ], 2);
        expect($res)->equal(2);
    });
    it('provides static magic constructors', function() {
        $qrCode = Barcode::qrCode('abc123');
        expect($qrCode instanceof QrCode)->equal(true);
    });
    it('will throw if static constructor is wrong', function() {
        expect(function() {
            Barcode::bad();
        })->throw(\BadMethodCallException::class, 'Method constructor bad does not exist for this ADT. Valid static constructors are: upc, qrCode');
    });
    it('can support multiple types of ADT static constructors', function() {
        $dir = Direction::north();
        $barCode = BarCode::qrCode('foo');
        expect(get_class($dir))->equal(North::class);
        expect(get_class($barCode))->equal(QrCode::class);
    });
});
