<?php
/**
 * CarWeb API
 *
 * PHP Version 5.4
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */

namespace BespokeSupport\CarWeb;

use BespokeSupport\DatabaseWrapper\AbstractDatabaseWrapper;
use GuzzleHttp\Client;

/**
 * Class CarWebApi
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWebApi
{
    /**
     * Domains
     *
     * @var array
     */
    public static $carWebDomains = [
        'www1.carwebuk.com',
        'www2.carwebuk.com',
        'www3.carwebuk.com',
    ];
    /**
     * Version
     *
     * @var string
     */
    public static $carWebVersion = '0.31.1';
    /**
     * URL
     *
     * @var string
     */
    public static $urlBase = "http://{{carWebDomain}}/CarweBVrrB2Bproxy/carwebVrrWebService.asmx/";

    /**
     * Call the API
     *
     * @param string                  $url       URL
     * @param array                   $callArray Params
     * @param AbstractDatabaseWrapper $database  Database
     *
     * @return CarWebEntity|CarWebError|null
     */
    public static function callApi(
        $url,
        array $callArray,
        AbstractDatabaseWrapper $database = null
    ) {
        $client = new Client(
            [
            ]
        );

        $responseObject = $client->get(
            $url,
            [
                'query' => $callArray,
                'verify' => false,

            ]
        );

        $xml = simplexml_load_string($responseObject->getBody());

        $returnEntity = CarWebConvert::xmlToEntity($xml);
        $returnEntity->dataSource = 'api';

        $type = ucwords(self::callType($callArray));
        $search = self::callSearch($callArray);

        $returnEntity->$type = $search;

        CarWebCacheDatabase::cacheDatabasePut($returnEntity, $database);

        CarWebCacheFile::cacheFilePut($returnEntity);

        return $returnEntity;
    }

    /**
     * What is the Search param
     *
     * @param array $callArray Params
     *
     * @return string
     */
    public static function callSearch(array $callArray)
    {
        if (!empty($callArray['strVRM'])) {
            return $callArray['strVRM'];
        } else {
            return $callArray['strVIN'];
        }
    }

    /**
     * Type of Call
     *
     * @param array $callArray Params
     *
     * @return string
     */
    public static function callType(array $callArray)
    {
        if (!empty($callArray['strVRM'])) {
            return 'vrm';
        } else {
            return 'vin';
        }
    }
}
