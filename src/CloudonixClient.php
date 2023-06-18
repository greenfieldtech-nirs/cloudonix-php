<?php
    /**
     * @package cloudonix-php
     * @file    CloudonixClient.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    namespace Cloudonix;

    use Cloudonix\Helpers\HttpHelper as HttpHelper;
    use Cloudonix\Helpers\LogHelper as LogHelper;

    use Cloudonix\Entities\Tenant as EntityTenant;
    use Cloudonix\Collections\Tenants as CollectionTenants;

    use Exception;

    require_once 'Helpers/ConfigHelper.php';

    /**
     * CloudonixClient Object
     */
    class CloudonixClient
    {
        public HttpHelper $httpConnector;

        /**
         * Construct the Cloudonix REST API Client
         *
         * @param string|null $apikey       A Cloudonix assigned API Key
         * @param string      $httpEndpoint The Cloudonix REST API Endpoint
         * @param float       $timeout      HTTP Client timeout
         * @param int         $debug        Debug Log level (see: Helpers/ConfigHelper.php)
         */
        public function __construct(string $apikey = null, string $httpEndpoint = HTTP_ENDPOINT, float $timeout = HTTP_TIMEOUT, int $debug = LOGGER_DISABLE)
        {
            try {
                $this->logger = new LogHelper($debug);
                $this->httpConnector = new HttpHelper($apikey, $httpEndpoint, $timeout, $debug);
            } catch (Exception $e) {
                die($e->getMessage() . '  code: ' . $e->getCode());
            }
        }

        public function httpConnector(): HttpHelper
        {
            return $this->httpConnector;
        }

        public function tenants(): CollectionTenants
        {
            if (!isset($this->tenants)) {
                $this->logger->debug("Starting Tenants Collection for the first time...");
                $this->tenants = new CollectionTenants($this);
            }
            return $this->tenants;
        }

        public function tenant(string $tenantId = "self"): EntityTenant
        {
            $this->logger->debug("Starting Tenant {$tenantId} for the first time...");
            $tenantObject = new EntityTenant($this, $tenantId);
            return $tenantObject->get();
        }

    }