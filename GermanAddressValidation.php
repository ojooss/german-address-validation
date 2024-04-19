<?php
/**
 * Author: ojooss
 * Copyright 2019
 */

class GermanAddressValidation
{

    const URL = 'https://www.postdirekt.de/plzserver/PlzAjaxServlet';

    /**
     * @var string
     */
    public $lastResult;

    /**
     * @param array $postFields
     * @return stdClass
     * @throws Exception
     */
    protected function request(array $postFields): stdClass
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::URL);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $this->lastResult = curl_exec($curl);
        if ($this->lastResult === false) {
            throw new Exception('Invalid server response');
        }
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($errno > 0) {
            throw new Exception($error);
        }
        $json = json_decode($this->lastResult);
        if (is_null($json)) {
            throw new Exception('Invalid server response');
        }
        if (!$json->success) {
            throw new Exception('Request failed');
        }
        return $json;
    }

    /**
     * @param string $postCode
     * @return stdClass[]
     * @throws Exception
     */
    public function searchCityByPostCode(string $postCode): array
    {
        if (!preg_match('~[0-9]{5}~', $postCode)) {
            throw new Exception('Invalid postcode');
        }

        $postFields = [
            'finda' => 'city',
            'city' => $postCode,
            'lang' => 'de_DE'
        ];
        $result = $this->request($postFields);
        return $result->rows ?? [];
    }

    /**
     * @param string $city
     * @param string $street
     * @return stdClass[]
     * @throws Exception
     */
    public function searchPostCodeByCityStreet(string $city, string $street): array
    {
        $postFields = [
            'finda' => 'plz',
            'plz_city' => $city,
            'plz_plz' => '',
            'plz_city_clear' => '',
            'plz_district' => '',
            'plz_street' => $street,
            'lang' => 'de_DE'
        ];
        $result = $this->request($postFields);
        return $result->rows ?? [];
    }

    /**
     * @param string $postCode
     * @return bool
     * @throws Exception
     */
    public function validatePostCode(string $postCode): bool
    {
        $cities = $this->searchCityByPostCode($postCode);
        return (count($cities) > 0);
    }

    /**
     * @param string $city
     * @param string $street
     * @param string $postCode
     * @return bool
     * @throws Exception
     */
    public function validateAddress(string $city, string $street, string $postCode): bool
    {
        $matches = $this->searchPostCodeByCityStreet($city, $street);
        foreach($matches as $address) {
            if ($address->plz == $postCode) {
                return true;
            }
        }
        return false;
    }
}
