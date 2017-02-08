<?php

namespace Tagliatti\BoletoValidator;

use Tagliatti\BoletoValidator\BoletoValidator;

/**
 * @author Filipe Tagliatti <filipetagliatti@gmail.com>
 */
class BoletoValidatorTest extends \PHPUnit_Framework_TestCase
{
        
    public function testConvenioValidModule10()
    {
        $this->assertTrue(BoletoValidator::convenio('83640000001-1 33120138000-2 81288462711-6 08013618155-1'));
    }
    
    public function testConvenioValidModule11()
    {
        $this->assertTrue(BoletoValidator::convenio('85890000460-9 52460179160-5 60759305086-5 83148300001-0'));
    }
    
    /**
     * Teste para cobrir 100% de coverage.
     */
    public function testConvenioValidModule11IfRestOfDivisiono10()
    {
        $this->assertTrue(BoletoValidator::convenio('85890000464-1 52460179160-5 60759305086-5 83148300001-0'));
    }
    
    public function testConvenioInalidModule10()
    {
        $this->assertFalse(BoletoValidator::convenio('83640000001-2 33120138000-2 81288462711-6 08013618155-1'));
    }

    public function testConvenioInalidModule11()
    {
        $this->assertFalse(BoletoValidator::convenio('85890000460-8 52460179160-5 60759305086-5 83148300001-0'));
    }
    
    /**
     * 1 caracter a mais
     * 
     * @expectedException Exception
     */
    public function testConvenioInvalidFormatWithOneMoreCharacter() {
        $this->assertFalse(BoletoValidator::convenio('85890000460-9 52460179160-5 60759305086-5 83148300001-09'));
    }
    
    /**
     * Passando uma letra
     * 
     * @expectedException Exception
     */
    public function testConvenioInvalidFormatPassingALetter()
    {
        $this->assertFalse(BoletoValidator::convenio('85890000460-9 52460179160-5 60759305086-5 83148300001-a'));
    }
    
    public function testBoletoValid()
    {
        $this->assertTrue(BoletoValidator::boleto('42297.11504 00001.954411 60020.034520 2 68610000054659'));
    }
    
    public function testBoletoInvalid()
    {
        $this->assertFalse(BoletoValidator::boleto('92297.11504 00001.954411 60020.034520 2 68610000054659'));
    }
    
    
    /**
     * 1 caracter a mais
     * 
     * @expectedException Exception
     */
    public function testBoletoInvalidFormatWithOneMoreCharacter()
    {
        $this->assertFalse(BoletoValidator::boleto('42297.11504 00001.954411 60020.034520 2 686100000546590'));
    }
    
    /**
     * Passando uma letra
     * 
     * @expectedException Exception
     */
    public function testBoletoInvalidFormatPassingALetter()
    {
        $this->assertFalse(BoletoValidator::boleto('42297.11504 00001.954411 60020.034520 2 6861000005465a'));
    }
        
}
