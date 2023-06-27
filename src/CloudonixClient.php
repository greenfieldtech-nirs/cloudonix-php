<?php
    /**
     * @package cloudonix-php
     * @file    CloudonixClient.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix;

    require_once 'Helpers/ConfigHelper.php';

    use Cloudonix\Helpers\HttpHelper as HttpHelper;
    use Cloudonix\Helpers\LogHelper as LogHelper;
    use Cloudonix\Entities\Tenant as EntityTenant;
    use Exception;

    /**
     * CloudonixClient Object
     */
    class CloudonixClient
    {
        public HttpHelper $httpConnector;
        public LogHelper $logger;

        /**
         * Construct the Cloudonix REST API Client
         *
         * @param ?string $apikey       A Cloudonix assigned API Key
         * @param string  $httpEndpoint The Cloudonix REST API Endpoint
         * @param float   $timeout      HTTP Client timeout
         * @param int     $debug        Debug Log level (see: Helpers/ConfigHelper.php)
         */
        public function __construct(?string $apikey, string $httpEndpoint = HTTP_ENDPOINT, float $timeout = HTTP_TIMEOUT, int $debug = LOGGER_DISABLE)
        {
            try {
                $this->logger = new LogHelper($debug);
                $this->httpConnector = new HttpHelper($apikey, $httpEndpoint, $timeout, $debug);
            } catch (Exception $e) {
                die($e->getMessage() . '  code: ' . $e->getCode());
            }
        }

        public function tenant(string $tenantId = "self"): EntityTenant
        {
            return new EntityTenant($this, $tenantId);
        }
    }