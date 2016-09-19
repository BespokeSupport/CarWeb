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
 * Class CarWebConvert
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWebConvert
{
    /**
     * Convert XML
     *
     * @param \SimpleXMLElement|null $xml XML
     *
     * @return CarWebEntity|CarWebError|null
     */
    public static function xmlToEntity(\SimpleXMLElement $xml)
    {
        /*
         * Errors
         */
        if (!$xml) {
            return null;
        } elseif (!isset($xml->DataArea) || !isset($xml->DataArea->Error)) {
            return null;
        } elseif (isset($xml->DataArea->Error->Details)
            && isset($xml->DataArea->Error->Details->ErrorCode)
            && $xml->DataArea->Error->Details->ErrorCode
        ) {
            return new CarWebError(
                trim((string)$xml->DataArea->Error->Details->ErrorDescription),
                (int)$xml->DataArea->Error->Details->ErrorCode,
                $xml->asXML()
            );
        }

        $result = array();

        $result['created'] = date('Y-m-d H:i:s');

        // @codingStandardsIgnoreStart
        $GrossWeight = preg_replace('/[^0-9\.]/', '', (string)$xml->DataArea->Vehicles->Vehicle->GrossWeight);
        $KerbWeightMin = preg_replace('/[^0-9\.]/', '', (string)$xml->DataArea->Vehicles->Vehicle->KerbWeightMin);
        $KerbWeightMax = preg_replace('/[^0-9\.]/', '', (string)$xml->DataArea->Vehicles->Vehicle->KerbWeightMax);
        $KerbWeight = ($KerbWeightMax) ?: $KerbWeightMin;
        $result['GrossWeight'] = $GrossWeight;
        $result['KerbWeight'] = $KerbWeight;
        $result['KerbWeightMin'] = $KerbWeightMin;
        $result['KerbWeightMax'] = $KerbWeightMax;
        //
        $result['Vin'] = trim((string)$xml->DataArea->Vehicles->Vehicle->VIN_Original_DVLA);
        $result['Vrm'] = trim((string)$xml->DataArea->Vehicles->Vehicle->VRM_Curr);
        $result['Make'] = trim((string)$xml->DataArea->Vehicles->Vehicle->Combined_Make);
        $result['ModelRange'] = trim((string)$xml->DataArea->Vehicles->Vehicle->ModelRangeDescription);
        $result['Model'] = trim((string)$xml->DataArea->Vehicles->Vehicle->ModelVariantDescription);
        $result['ModelFull'] = trim((string)$xml->DataArea->Vehicles->Vehicle->Combined_Model);
        $result['Body'] = trim((string)$xml->DataArea->Vehicles->Vehicle->BodyStyleDescription);
        $result['Colour'] = trim((string)$xml->DataArea->Vehicles->Vehicle->ColourCurrent);
        $result['EngineSize'] = trim((string)$xml->DataArea->Vehicles->Vehicle->Combined_EngineCapacity);
        $result['EngineModelCode'] = trim((string)$xml->DataArea->Vehicles->Vehicle->EngineModelCode);
        $result['Fuel'] = trim((string)$xml->DataArea->Vehicles->Vehicle->Combined_FuelType);
        $result['TotalPreviousKeepers'] = trim((string)$xml->DataArea->Vehicles->Vehicle->NumberOfPreviousKeepers);
        $result['DateFirstRegistered'] = trim((string)$xml->DataArea->Vehicles->Vehicle->DateFirstRegistered);
        $result['YearManufactured'] = trim((string)$xml->DataArea->Vehicles->Vehicle->DVLAYearOfManufacture);
        $result['Transmission'] = trim((string)$xml->DataArea->Vehicles->Vehicle->Combined_Transmission);
        $result['Gears'] = trim((string)$xml->DataArea->Vehicles->Vehicle->Combined_ForwardGears);
        $result['VehicleImageUrl'] = trim((string)$xml->DataArea->Vehicles->Vehicle->VehicleImageUrl);

        $result['provider'] = 'carweb';
        // @codingStandardsIgnoreEnd

        $result['apiData'] = $xml->asXML();

        $entity = new CarWebEntity($result);

        return $entity;
    }
}
