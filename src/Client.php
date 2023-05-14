<?php

    namespace Cloudonix;

    use Cloudonix\Datamodel\Domains;
    use Cloudonix\Datamodel\Tenant;

    use Cloudonix\DataModel\Entities\Domain as CloudonixDomain;

    use Cloudonix\Helpers\HttpHelper as HttpHelper;
    use Cloudonix\Helpers\LogHelper as LogHelper;
    use Exception;
    use Phpfastcache\CacheManager;
    use Phpfastcache\Config\ConfigurationOption;
    use Phpfastcache\Helper\Psr16Adapter;

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
        /** @var Psr16Adapter The Cache Manager Object */
        public Psr16Adapter $cacheHandler;

        /** @var HttpHelper Guzzle HTTP Client Connector */
        public HttpHelper $httpConnector;

        private string $httpEndpoint;
        private string $cacheDirectory;

        /** @var LogHelper Logger */
        public LogHelper $logger;

        /** @var bool Debug HTTP Requests */
        public bool $httpDebug = false;

        /** @var Tenant Cloudonix Tenant Object */
        protected Tenant $tenantObject;

        /** @var Domains Cloudonix Domains Collection Object */
        protected Domains $domainsObject;

        /** @var array Cloudonix Domain Objects Collection */
        protected array $domainObjectsCollection;

        public function __construct($apikey = null, $httpEndpoint = HTTP_ENDPOINT, $cacheDirectory = null, $timeout = HTTP_TIMEOUT, $debug = DISABLE, $httpDebug = false)
        {
            try {

                $this->httpDebug = $httpDebug;

                $this->logger = new LogHelper($debug);
                $this->logger->debug("cloudonix-php is starting");

                $this->httpEndpoint = (($httpEndpoint != null) && (strlen($httpEndpoint))) ? $httpEndpoint : HTTP_ENDPOINT;
                $this->logger->debug("Remote HTTP Endpoint is now $this->httpEndpoint");

                $this->cacheDirectory = (($cacheDirectory != null) && (strlen($cacheDirectory))) ? $cacheDirectory : sys_get_temp_dir();
                CacheManager::setDefaultConfig(new ConfigurationOption([
                    'path' => $this->cacheDirectory
                ]));
                $this->cacheHandler = new Psr16Adapter(CACHE_DRIVER);

                $mySanityCheckValue = uniqid("", TRUE);
                $this->cacheHandler->set("mySanityValue", $mySanityCheckValue);

                $mySanityReadValue = $this->cacheHandler->get('mySanityValue');
                if ($mySanityCheckValue != $mySanityReadValue)
                    throw new Exception('Cache engine not properly working, bailing out', 500, null);

                $this->logger->debug("Cache handler successfully initiated");

                $this->cacheHandler->delete("mySanityValue");
                $this->httpConnector = new HttpHelper($apikey, $httpEndpoint, $timeout, $httpDebug);

            } catch (Exception $e) {
                die($e->getMessage() . '  code: ' . $e->getCode());
            }
        }

        /**
         * Client Destructor
         */
        public function __destruct()
        {
            $this->cacheHandler->clear();
        }

        public function tenant(string $tenantIdent = "self"): Tenant
        {
            if (!isset($this->tenantObject)) {
                $this->tenantObject = new Tenant($this, $tenantIdent);
            }
            return $this->tenantObject;
        }

        public function domains(string $tenantIdent = "self"): Domains
        {
            if (!isset($this->domainsObject)) {
                $this->domainsObject = new Domains($this, $tenantIdent);
            }
            return $this->domainsObject;
        }

        /**
         * @param string $domainIdent
         *
         * @return CloudonixDomain
         */
        public function domain(string $domainIdent): CloudonixDomain
        {
            $this->domainObjectsCollection[] = new CloudonixDomain($domainIdent, $this);
            return end($this->domainObjectsCollection);
        }

    }