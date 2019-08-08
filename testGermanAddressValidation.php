<?php
/**
 * Author: ojooss
 * Copyright 2019
 */

require_once __DIR__.'/GermanAddressValidation.php';

use PHPUnit\Framework\TestCase;

final class GermanAddressValidationTest extends TestCase
{

    /**
     * @var GermanAddressValidation
     */
    protected $testObject;

    public function setUp()
    {
        $this->testObject = new GermanAddressValidation();
    }

    /**
     * @return array
     */
    public function providerSearchCityByPostCode()
    {
        return array(
            array('80636', 'München'),
            array('30179', 'Hannover'),
            array('09456', 'Annaberg-Buchholz'),
            array('00636', 'invalid'),
        );
    }

    /**
     * @param $postCode
     * @param $expected
     *
     * @dataProvider providerSearchCityByPostCode
     */
    public function testSearchCityByPostCode($postCode, $expected)
    {
        $matches = $this->testObject->searchCityByPostCode($postCode);
        $city = (count($matches) > 0)?$matches[0]->city:'invalid';
        $this->assertEquals( $expected, $city);
    }


    /**
     * @return array
     */
    public function providerValidatePostCode()
    {
        return array(
            array('80636', true),
            array('00636', false),
        );
    }

    /**
     * @param $postCode
     * @param $expected
     *
     * @dataProvider providerValidatePostCode
     */
    public function testValidatePostCode($postCode, $expected)
    {
        $this->assertEquals( $expected, $this->testObject->validatePostCode($postCode));
    }


    /**
     * @return array
     */
    public function providerSearchPostCodeByCityStreet()
    {
        return array(
            array('München', 'Erika-Mann-Straße 33',  '80636'),
            array('München', 'Konrad-Adenauer-Straße 17',  'invalid'),
        );
    }

    /**
     * @param $postCode
     * @param $expected
     *
     * @dataProvider providerSearchPostCodeByCityStreet
     */
    public function testSearchPostCodeByCityStreet($city, $street, $expected)
    {
        $matches = $this->testObject->searchPostCodeByCityStreet($city, $street);
        $postCode = (count($matches) > 0)?$matches[0]->plz:'invalid';
        $this->assertEquals( $expected, $postCode);
    }


    /**
     * @return array
     */
    public function providerValidateAddress()
    {
        return array(
            array('München', 'Erika-Mann-Straße 33', '80636', true),
            array('München', 'Erika-Mann-Straße 33', '80686', false),
        );
    }

    /**
     * @param $postCode
     * @param $expected
     *
     * @dataProvider providerValidateAddress
     */
    public function testValidateAddress($city, $street, $postCode, $expected)
    {
        $this->assertEquals( $expected, $this->testObject->validateAddress($city, $street, $postCode));
    }

}

