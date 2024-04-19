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
    protected GermanAddressValidation $testObject;

    public function setUp(): void
    {
        $this->testObject = new GermanAddressValidation();
    }

    /**
     * @return array
     */
    public static function providerSearchCityByPostCode(): array
    {
        return array(
            array('80636', 'München'),
            array('30179', 'Hannover'),
            array('09456', 'Annaberg-Buchholz'),
            array('00636', 'invalid'),
        );
    }

    /**
     * @param string $postCode
     * @param string $expected
     *
     * @throws Exception
     * @dataProvider providerSearchCityByPostCode
     */
    public function testSearchCityByPostCode(string $postCode, string $expected): void
    {
        $matches = $this->testObject->searchCityByPostCode($postCode);
        $city = (count($matches) > 0)?$matches[0]->city:'invalid';
        $this->assertEquals( $expected, $city);
    }


    /**
     * @return array
     */
    public static function providerValidatePostCode(): array
    {
        return array(
            array('80636', true),
            array('00636', false),
        );
    }

    /**
     * @param string $postCode
     * @param bool $expected
     *
     * @throws Exception
     * @dataProvider providerValidatePostCode
     */
    public function testValidatePostCode(string $postCode, bool $expected): void
    {
        $this->assertEquals( $expected, $this->testObject->validatePostCode($postCode));
    }


    /**
     * @return array
     */
    public static function providerSearchPostCodeByCityStreet(): array
    {
        return array(
            array('München', 'Erika-Mann-Straße 33',  '80636'),
            array('München', 'Konrad-Adenauer-Straße 17',  'invalid'),
        );
    }

    /**
     * @param string $city
     * @param string $street
     * @param string $expected
     *
     * @throws Exception
     * @dataProvider providerSearchPostCodeByCityStreet
     */
    public function testSearchPostCodeByCityStreet(string $city, string $street, string $expected): void
    {
        $matches = $this->testObject->searchPostCodeByCityStreet($city, $street);
        $postCode = (count($matches) > 0)?$matches[0]->plz:'invalid';
        $this->assertEquals( $expected, $postCode);
    }


    /**
     * @return array
     */
    public static function providerValidateAddress(): array
    {
        return array(
            array('München', 'Erika-Mann-Straße 33', '80636', true),
            array('München', 'Erika-Mann-Straße 33', '80686', false),
        );
    }

    /**
     * @param string $city
     * @param string $street
     * @param string $postCode
     * @param bool $expected
     *
     * @throws Exception
     * @dataProvider providerValidateAddress
     */
    public function testValidateAddress(string $city, string $street, string $postCode, bool $expected): void
    {
        $this->assertEquals( $expected, $this->testObject->validateAddress($city, $street, $postCode));
    }
}
