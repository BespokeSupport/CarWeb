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

/**
 * Class CarWebCacheFile
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWebCacheFile
{
    /**
     * Location of Cache Directory
     *
     * @var null|string
     */
    protected static $cacheDirectory = null;

    /**
     * GET from Cache
     *
     * @param string $search VRM / VIN
     *
     * @return null|CarWebEntity
     */
    public static function cacheFileGet($search)
    {
        if (!self::$cacheDirectory) {
            return null;
        }

        if (file_exists(self::$cacheDirectory . $search)) {
            try {
                $xml = simplexml_load_file(self::$cacheDirectory . $search);
            } catch (\Exception $e) {
                $xml = null;
            }

            $entity = CarWebConvert::xmlToEntity($xml);
            $entity->dataSource = 'file';
            return $entity;
        }

        return null;
    }

    /**
     * PUT to Cache
     *
     * @param CarWebEntity|CarWebError $entity Input class
     *
     * @return void
     */
    public static function cacheFilePut($entity)
    {
        if (self::$cacheDirectory) {
            if (!empty($entity->Vrm)) {
                $subDir = (file_exists(self::$cacheDirectory . '/vrm')) ? '/vrm/' : '';
                file_put_contents(self::$cacheDirectory . $subDir . $entity->Vrm, $entity->apiData);
            }
            if (!empty($entity->Vin)) {
                $subDir = (file_exists(self::$cacheDirectory . '/vin')) ? '/vin/' : '';
                file_put_contents(self::$cacheDirectory . $subDir . $entity->Vin, $entity->apiData);
            }
        }
    }

    /**
     * Set Cache Directory
     *
     * @param string $directory Directory
     *
     * @throws \ErrorException
     * @return void
     */
    public static function setCacheDirectory($directory)
    {
        if (!is_writable($directory)) {
            throw new \ErrorException('Directory not writable | ' . $directory);
        }

        $directory = rtrim($directory . '/') . '/';

        self::$cacheDirectory = $directory;
    }
}
