<?php
/**
 * CarWeb API.
 *
 * PHP Version 5.4
 *
 * @category API
 *
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/BespokeSupport/CarWeb
 */

namespace BespokeSupport\CarWeb;

/**
 * Class CarWebEntity.
 *
 * @category API
 *
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWebEntity extends \ArrayObject implements \JsonSerializable
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
     * Constructor.
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

    /**
     * JSON array.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'Body'                    => $this->Body,
            'Colour'                  => $this->Colour,
            'DateFirstRegistered'     => $this->DateFirstRegistered,
            'DateScrapped'            => $this->DateScrapped,
            'Doors'                   => $this->Doors,
            'EngineModelCode'         => $this->EngineModelCode,
            'EngineSize'              => $this->EngineSize,
            'Fuel'                    => $this->Fuel,
            'Gears'                   => $this->Gears,
            'GrossWeight'             => $this->GrossWeight,
            'IsImported'              => $this->IsImported,
            'KerbWeight'              => $this->KerbWeight,
            'KerbWeightMax'           => $this->KerbWeightMax,
            'KerbWeightMin'           => $this->KerbWeightMin,
            'LastChangedOfKeeperDate' => $this->LastChangedOfKeeperDate,
            'Make'                    => $this->Make,
            'Mileage'                 => $this->Mileage,
            'Model'                   => $this->Model,
            'ModelComplete'           => trim($this->ModelRange.' '.$this->Model),
            'ModelRange'              => $this->ModelRange,
            'TotalPreviousKeepers'    => $this->TotalPreviousKeepers,
            'Transmission'            => $this->Transmission,
            'VehicleImageUrl'         => $this->VehicleImageUrl,
            'VehicleImageUrlComplete' => $this->VehicleImageUrlComplete,
            'Vin'                     => $this->Vin,
            'Vrm'                     => $this->Vrm,
            'YearManufactured'        => $this->YearManufactured,
            'created'                 => $this->created,
            'provider'                => $this->provider,
            'dataSource'              => 'api',
        ];
    }
}
