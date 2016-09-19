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

/**
 * Class CarWebCacheDatabase
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWebCacheDatabase
{
    /**
     * Database Table
     *
     * @var null|string
     */
    public static $cacheDatabaseTable = 'vehicle';

    /**
     * GET Cache
     *
     * @param string                  $search     Search
     * @param string                  $searchType Type
     * @param AbstractDatabaseWrapper $database   Database
     *
     * @return CarWebEntity|null
     */
    public static function cacheDatabaseGet(
        $search,
        $searchType = 'vrm',
        AbstractDatabaseWrapper $database = null
    ) {
        if (!$database) {
            return null;
        }

        $result = $database->findOneBy(
            self::$cacheDatabaseTable, array(
                $searchType => $search
            )
        );

        if ($result) {
            $entity = new CarWebEntity($result);
            $entity->dataSource = 'database';
            return $entity;
        }

        return null;
    }

    /**
     * PUT cache
     *
     * @param CarWebEntity            $entity   Entity
     * @param AbstractDatabaseWrapper $database Database
     *
     * @return bool
     */
    public static function cacheDatabasePut(
        $entity = null,
        AbstractDatabaseWrapper $database = null
    ) {
        if ($entity instanceof CarWebEntity && $database) {
            try {
                $data = array(
                    'Body' => $entity->Body,
                    'Colour' => $entity->Colour,
                    'DateFirstRegistered' => $entity->DateFirstRegistered,
                    'DateScrapped' => $entity->DateScrapped,
                    'Doors' => $entity->Doors,
                    'EngineSize' => $entity->EngineSize,
                    'Fuel' => $entity->Fuel,
                    'Gears' => $entity->Gears,
                    'VehicleImageUrl' => $entity->VehicleImageUrl,
                    'IsImported' => $entity->IsImported,
                    'KerbWeight' => $entity->KerbWeight,
                    'KerbWeightMin' => $entity->KerbWeightMin,
                    'KerbWeightMax' => $entity->KerbWeightMax,
                    'GrossWeight' => $entity->GrossWeight,
                    'LastChangedOfKeeperDate' => $entity->LastChangedOfKeeperDate,
                    'Make' => $entity->Make,
                    'Mileage' => $entity->Mileage,
                    'Model' => $entity->Model,
                    'ModelRange' => $entity->ModelRange,
                    'TotalPreviousKeepers' => $entity->TotalPreviousKeepers,
                    'Transmission' => $entity->Transmission,
                    'Vin' => $entity->Vin,
                    'Vrm' => $entity->Vrm,
                    'YearManufactured' => $entity->YearManufactured,
                    'created' => $entity->created,
                    'dataSource' => $entity->dataSource,
                    'provider' => $entity->provider,
                );

                $database->insert(self::$cacheDatabaseTable, $data);

                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
}
