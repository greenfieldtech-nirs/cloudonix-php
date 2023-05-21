<?php

    namespace Cloudonix;

    use Cloudonix\Datamodel\Domains as CloudonixDomains;
    use Cloudonix\Datamodel\Tenant as CloudonixTenant;

    use Cloudonix\DataModel\Entities\Domain as EntityDomain;

    use Cloudonix\Helpers\HttpHelper as HttpHelper;
    use Cloudonix\Helpers\LogHelper as LogHelper;
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
     * @file    CloudonixClient.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @created 2023-05-09
     */
    class Client
    {
        /** @var HttpHelper Guzzle HTTP Client Connector */
        public HttpHelper $httpConnector;

        private string $httpEndpoint;

        /** @var LogHelper Logger */
        public LogHelper $logger;

        /** @var bool Debug HTTP Requests */
        public bool $httpDebug = false;

        /** @var CloudonixTenant Cloudonix Tenant Object */
        protected CloudonixTenant $tenantObject;

        /** @var CloudonixDomains Cloudonix Domains Collection Object */
        protected CloudonixDomains $domainsObject;

        /**
         * Construct the Cloudonix REST API Client
         *
         * @param string $apikey       A Cloudonix assigned API Key
         * @param string $httpEndpoint The Cloudonix REST API Endpoint
         * @param float  $timeout      HTTP Client timeout
         * @param int    $debug        Debug Log level (see: Helpers/ConfigHelper.php)
         * @param bool   $httpDebug    GuzzleHttp Client debug output
         */
        public function __construct($apikey = null, $httpEndpoint = HTTP_ENDPOINT, $timeout = HTTP_TIMEOUT, $debug = DISABLE, $httpDebug = false)
        {
            try {

                $this->httpDebug = $httpDebug;

                $this->logger = new LogHelper($debug);
                $this->logger->debug("cloudonix-php is starting");

                $this->httpEndpoint = (($httpEndpoint != null) && (strlen($httpEndpoint))) ? $httpEndpoint : HTTP_ENDPOINT;
                $this->logger->debug("Remote HTTP Endpoint is now $this->httpEndpoint");

                $this->httpConnector = new HttpHelper($apikey, $httpEndpoint, $timeout, $httpDebug);

            } catch (Exception $e) {
                die($e->getMessage() . '  code: ' . $e->getCode());
            }
        }

        /**
         * Return a Cloudonix Tenant singleton object
         *
         * @param string $tenantIdent A Cloudonix Tenant name or numeric ID
         *
         * @return CloudonixTenant
         */
        public function tenant(string $tenantIdent = "self"): CloudonixTenant
        {
            if (!isset($this->tenantObject)) {
                $this->tenantObject = new CloudonixTenant($this, $tenantIdent);
            }
            return $this->tenantObject;
        }

        /**
         * Return a Cloudonix Domains singleton object
         *
         * @param string $tenantIdent A Cloudonix Tenant name or numeric ID
         *
         * @return CloudonixDomains
         */
        public function domains(string $tenantIdent = "self"): CloudonixDomains
        {
            if (!isset($this->domainsObject)) {
                $this->domainsObject = new CloudonixDomains($this, $tenantIdent);
            }
            return $this->domainsObject;
        }

        /**
         * Return a Cloudonix Domain object
         *
         * @param string $domainIdent A Cloudonix domain name or numeric ID
         *
         * @return EntityDomain
         */
        public function domain(string $domainIdent): EntityDomain
        {
            return new EntityDomain($domainIdent, $this);
        }

    }