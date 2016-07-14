<?php
    namespace Tagliatti\BoletoValidator;

    use Tagliatti\BoletoValidator\BoletoValidator;

    class BoletoValidatorTest extends \PHPUnit_Framework_TestCase {
        public function testPushAndPop() {
            $this->assertTrue(BoletoValidator::codigoBarras('83640000001-1 33120138000-2 81288462711-6 08013618155-1'));
        }
    }