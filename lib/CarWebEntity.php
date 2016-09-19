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
 * Class CarWebEntity
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWebEntity extends \ArrayObject
{
    public $Body;
    public $Colour;
    public $DateFirstRegistered;
    public $DateScrapped;
    public $Doors;
    public $EngineModelCode;
    public $EngineSize;
    public $Fuel;
    public $Gears;
    public $GrossWeight;
    public $IsImported;
    public $KerbWeight;
    public $KerbWeightMax;
    public $KerbWeightMin;
    public $LastChangedOfKeeperDate;
    public $Make;
    public $Mileage;
    public $Model;
    public $ModelRange;
    public $TotalPreviousKeepers;
    public $Transmission;
    public $VehicleImageUrl;
    public $VehicleImageUrlComplete;
    public $Vin;
    public $Vrm;
    public $YearManufactured;
    public $apiData;
    public $created;
    public $dataSource = 'api';
    public $provider;

    /**
     * Constructor
     *
     * @param array|null|object $inject Create
     */
    public function __construct($inject)
    {
        if ($inject) {
            if ($inject instanceof \stdClass) {
                $inject = get_object_vars($inject);
            }

            if (is_array($inject)) {
                foreach ($inject as $k => $v) {
                    if (property_exists($this, $k)) {
                        $this->$k = $v;
                    }
                }
            }
        }
    }
}
