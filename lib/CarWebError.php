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
 * Class CarWebError
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWebError extends \Exception
{
    const ERROR_LOCAL_ONLY = 'No vehicles returned (from cache)';
    const ERROR_PARAMS = 'Required parameter not found (Username, Password, Key)';
    public $Vin;
    public $Vrm;
    public $apiData;

    /**
     * CarWebError constructor.
     *
     * @param string $errorMessage Message
     * @param null   $errorCode    Code
     * @param null   $apiData      Data
     */
    public function __construct($errorMessage, $errorCode = null, $apiData = null)
    {
        $this->message = $errorMessage;
        $this->code = $errorCode;
        $this->apiData = $apiData;
    }
}
