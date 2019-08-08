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
     * @param array $postfields
     * @return json
     * @throws Exception
     */
    protected function request(array $postfields)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::URL);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postfields));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $this->lastResult = curl_exec($curl);
        if ($this->lastResult === false) {
            throw new \Exception('Invalid server response');
        }
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($errno > 0) {
            throw new \Exception($error);
        }
        $json = json_decode($this->lastResult);
        if (is_null($json)) {
            throw new \Exception('Invalid server response');
        }
        if (!$json->success) {
            throw new \Exception('Request failed');
        }
        return $json;
    }

    /**
     * @param string $postCode
     * @return stdClass[]
     * @throws Exception
     */
    public function searchCityByPostCode($postCode)
    {
        if (!preg_match('~[0-9]{5}~', $postCode)) {
            throw new \Exception('Invalid postcode');
        }

        $postFields = [
            'finda' => 'city',
            'city' => $postCode,
            'lang' => 'de_DE'
        ];
        $result = $this->request($postFields);
        if (isset($result->rows)) {
            return $result->rows;
        }
        else {
            return array();
        }
    }

    /**
     * @param string $city
     * @param string $street
     * @return stdClass[]
     * @throws Exception
     */
    public function searchPostCodeByCityStreet($city, $street)
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
        if (isset($result->rows)) {
            return $result->rows;
        }
        else {
            return array();
        }
    }

    /**
     * @param string $postCode
     * @return bool
     * @throws Exception
     */
    public function validatePostCode($postCode)
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
    public function validateAddress($city, $street, $postCode)
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
