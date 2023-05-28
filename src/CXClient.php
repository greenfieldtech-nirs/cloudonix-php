<?php

    namespace Cloudonix;

    use Cloudonix\Helpers\HttpHelper as HttpHelper;
    use Cloudonix\Helpers\LogHelper as LogHelper;

    use Cloudonix\Entities\Tenant as EntityTenant;
    use Cloudonix\Collections\Tenants as CollectionTenants;

    use Exception;

    require_once 'Helpers/ConfigHelper.php';

    /**
     * <code>
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     * </code>
     *
     * @project cloudonix-php
     * @file    CxClient.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @created 2023-05-09
     */
    class CXClient
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
        public function __construct(string $apikey = null, string $httpEndpoint = HTTP_ENDPOINT, float $timeout = HTTP_TIMEOUT, int $debug = DISABLE)
        {
            try {
                $this->logger = new LogHelper($debug);
                $this->logger->debug("CXClient is starting");
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
                $this->logger->debug("Starting Tenants for the first time...");
                $this->tenants = new CollectionTenants($this);
            }
            return $this->tenants;
        }

        public function tenant(string $tenantId = "self"): EntityTenant
        {
            $tenantObject = new EntityTenant($this, $tenantId);
            return $tenantObject->get();
        }

    }