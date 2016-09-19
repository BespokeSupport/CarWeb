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
use BespokeSupport\Reg\Reg;

/**
 * Class CarWeb
 *
 * @category API
 * @package  BespokeSupport\CarWeb
 * @author   Richard Seymour <web@bespoke.support>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/BespokeSupport/CarWeb
 */
class CarWeb
{
    /**
     * Reference for API
     *
     * @var null|string
     */
    protected $carWebClient = null;
    /**
     * Domain used
     *
     * @var null|string
     */
    protected $carWebDomainKeyUsed = null;
    /**
     * Used domains
     *
     * @var array
     */
    protected $carWebDomainsUsed = [];
    /**
     * Required Vars
     *
     * @var array
     */
    protected $credentialsArray = [
        "strUserName" => null,
        "strPassword" => null,
        "strKey1" => null,
    ];
    /**
     * Database
     *
     * @var AbstractDatabaseWrapper
     */
    protected $database;
    /**
     * Development
     *
     * @var bool
     */
    protected $isDevelopment = false;
    /**
     * Age of Cache
     *
     * @var int
     */
    protected $maxAgeOfCachedResult = 32;
    /**
     * Only cache
     *
     * @var bool
     */
    protected $useCacheOnly = false;

    /**
     * Constructor
     *
     * @param string $strUserName  User
     * @param string $strPassword  Pass
     * @param string $strKey       Key
     * @param null   $strClientRef Reference
     */
    public function __construct(
        $strUserName,
        $strPassword,
        $strKey,
        $strClientRef = null
    ) {
        if (empty($strUserName) || empty($strPassword) || empty($strKey)) {
            throw new \InvalidArgumentException(CarWebError::ERROR_PARAMS);
        }

        $this->credentialsArray['strUserName'] = $strUserName;
        $this->credentialsArray['strPassword'] = $strPassword;
        $this->credentialsArray['strKey1'] = $strKey;
        $this->carWebClient = $strClientRef;
    }

    /**
     * Which search are we using?
     *
     * @param string $search Search
     *
     * @return string
     */
    public static function lookupType($search)
    {
        if (strlen($search) == 17) {
            $type = 'vin';
        } else {
            $reg = Reg::create($search);
            if ($reg) {
                $type = 'vrm';
            } else {
                $type = 'vin';
            }
        }

        return $type;
    }

    /**
     * Randomly choose a CarWeb domain
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getApiDomain()
    {
        $domainsAvailable = CarWebApi::$carWebDomains;

        while (is_null($this->carWebDomainKeyUsed)) {
            $this->carWebDomainKeyUsed = array_rand($domainsAvailable, 1);
            if (in_array($this->carWebDomainKeyUsed, $this->carWebDomainsUsed)) {
                $this->carWebDomainKeyUsed = null;
            }
            if (count($this->carWebDomainsUsed) == count($domainsAvailable)) {
                throw new \Exception('Cannot call CarWebApi');
            }
        }

        return $domainsAvailable[$this->carWebDomainKeyUsed];
    }

    /**
     * Build URL
     *
     * @param string $endpoint API
     *
     * @return string
     */
    protected function getApiUrl($endpoint)
    {
        $domain = $this->getApiDomain();
        return str_replace('{{carWebDomain}}', $domain, CarWebApi::$urlBase) . $endpoint;
    }

    /**
     * Add domain to Image
     *
     * @param CarWebEntity $carWebEntity Entity
     *
     * @return CarWebEntity
     */
    public function entitySetImageDomain(CarWebEntity $carWebEntity)
    {
        if (!empty($carWebEntity->VehicleImageUrl)) {
            $domain = ($this->carWebDomainKeyUsed) ?
                CarWebApi::$carWebDomains[$this->carWebDomainKeyUsed] :
                $this->getApiDomain();
            $carWebEntity->VehicleImageUrlComplete = 'https://' . $domain . $carWebEntity->VehicleImageUrl;
        }

        return $carWebEntity;
    }

    /**
     * In Development?
     *
     * @return string
     */
    public function getVersion()
    {
        if ($this->isDevelopment) {
            return preg_replace('/(\d{1,2})$/', 'test', CarWebApi::$carWebVersion);
        }

        return CarWebApi::$carWebVersion;
    }

    /**
     * Main search
     *
     * @param string $search          Search
     * @param bool   $exceptionOnNull Exception
     *
     * @return CarWebEntity|CarWebError|null
     */
    public function lookup($search, $exceptionOnNull = false)
    {
        $search = preg_replace('/[^A-Z0-9]/', '', strtoupper($search));

        $type = self::lookupType($search);

        switch ($type) {
        case 'vrm':
            $entity = $this->lookupVrm($search, $exceptionOnNull);
            break;
        case 'vin':
            $entity = $this->lookupVin($search, $exceptionOnNull);
            break;
        default:
            $entity = null;
        }

        if ($entity instanceof CarWebEntity) {
            $entity = $this->entitySetImageDomain($entity);
        }

        return $entity;
    }

    /**
     * VIN lookup
     *
     * @param string $search          Search
     * @param bool   $exceptionOnNull Exception
     *
     * @return CarWebEntity|CarWebError|null Entity
     * @throws CarWebError
     */
    public function lookupVin($search, $exceptionOnNull = false)
    {
        $search = preg_replace('#[^0-9A-Z]#', '', strtoupper($search));

        if (!strlen($search)) {
            throw new \InvalidArgumentException('Unknown search term');
        }

        $return = CarWebCacheFile::cacheFileGet($search);
        if ($return) {
            return $return;
        }

        $return = CarWebCacheDatabase::cacheDatabaseGet($search, 'Vin', $this->database);
        if ($return) {
            return $return;
        }

        if (!$return && $this->useCacheOnly) {
            if ($exceptionOnNull) {
                throw new CarWebError(CarWebError::ERROR_LOCAL_ONLY);
            } else {
                return new CarWebError(CarWebError::ERROR_LOCAL_ONLY);
            }
        }

        $callArray = array_merge(
            $this->credentialsArray,
            [
                'strVIN' => $search,
                'strVersion' => $this->getVersion(),
                'strClientRef' => $this->carWebClient,
                'strClientDescription' => $this->carWebClient
            ]
        );

        $apiData = CarWebApi::callApi(
            $this->getApiUrl('strB2BGetVehicleByVIN'),
            $callArray,
            $this->database
        );

        return $apiData;
    }

    /**
     * VRM Lookup
     *
     * @param string $search          Search
     * @param bool   $exceptionOnNull Exception
     *
     * @return CarWebEntity|CarWebError|null Entity
     * @throws CarWebError
     */
    public function lookupVrm($search, $exceptionOnNull = false)
    {
        $search = preg_replace('#[^0-9A-Z]#', '', strtoupper($search));

        if (!strlen($search)) {
            throw new \InvalidArgumentException('Unknown search term');
        }

        $return = CarWebCacheFile::cacheFileGet($search);
        if ($return) {
            return $return;
        }

        $return = CarWebCacheDatabase::cacheDatabaseGet($search, 'Vrm', $this->database);
        if ($return) {
            return $return;
        }

        if (!$return && $this->useCacheOnly) {
            if ($exceptionOnNull) {
                throw new CarWebError(CarWebError::ERROR_LOCAL_ONLY);
            } else {
                return new CarWebError(CarWebError::ERROR_LOCAL_ONLY);
            }
        }

        $callArray = array_merge(
            $this->credentialsArray,
            [
                'strVRM' => $search,
                'strVersion' => $this->getVersion(),
                'strClientRef' => $this->carWebClient,
                'strClientDescription' => $this->carWebClient
            ]
        );

        $apiData = CarWebApi::callApi(
            $this->getApiUrl('strB2BGetVehicleByVRM'),
            $callArray,
            $this->database
        );

        return $apiData;
    }

    /**
     * Cache DIR
     *
     * @param string $cacheDirectory DIR
     *
     * @return void
     */
    public function setCacheFileDirectory($cacheDirectory)
    {
        CarWebCacheFile::setCacheDirectory($cacheDirectory);
    }

    /**
     * Only use Cache
     *
     * @param bool $bool Bool
     *
     * @return void
     */
    public function setCacheOnly($bool)
    {
        $this->useCacheOnly = ($bool) ? true : false;
    }

    /**
     * Client Name
     *
     * @param string $client ClientName
     *
     * @return void
     */
    public function setCarWebClient($client)
    {
        $this->carWebClient = $client;
    }

    /**
     * Set Database connection
     *
     * @param AbstractDatabaseWrapper $database Database
     *
     * @return void
     */
    public function setDatabase(AbstractDatabaseWrapper $database = null)
    {
        $this->database = $database;
    }

    /**
     * Development?
     *
     * @param bool $bool Flag
     *
     * @return void
     */
    public function setDevelopment($bool)
    {
        $this->isDevelopment = ($bool) ? true : false;
    }

    /**
     * Ignore age of cache
     *
     * @return void
     */
    public function setIgnoreCacheThreshold()
    {
        $this->maxAgeOfCachedResult = null;
    }
}
