<?php
    namespace Cloudonix\DataModel;

    use Cloudonix\Client as CloudonixClient;
    use Cloudonix\DataModel\Commons\CloudonixClientObject as CloudonixClientObject;
    use Cloudonix\DataModel\Commons\CloudonixCollection as CloudonixCollection;

    use Cloudonix\DataModel\Entities\Domain as CloudonixDomain;

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
     * Domains provides an interface to the Cloudonix Domain DataModel - with a focus
     * on providing a top-level view of a Cloudonix Tenant domains collection.
     *
     * Example Usage:
     *
     * ```
     * ```
     *
     * @package cloudonixPhp
     * @file    Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=domains
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-09
     */
    class Domains extends CloudonixClientObject
    {
        /**
         * @var string Tenant name or numeric ID
         */
        protected string $tenantIdent;

        /**
         * @var CloudonixClient An instance of the CloudonixClient object
         */
        public CloudonixClient $client;

        /**
         * @var CloudonixCollection Cloudonix domains collection object
         */
        private CloudonixCollection $domainsCollection;

        /**
         * @var string The Cloudonix REST API Canonical path
         */
        private string $modelBasePath = "domains";

        /**
         * Domain DataModel Object Constructor
         *
         * @param CloudonixClient $client A CloudonixClient HTTP Connector Object
         * @param string          $name   A Tenant name or numeric ID (treated as string)
         */
        public function __construct(CloudonixClient $client, string $name = "self")
        {
            $this->tenantIdent = $name;
            parent::__construct($client);
            $this->client = $client;
        }

        /**
         * Return the data model associated canonical path
         *
         * @return string
         */
        public function getPath(): string
        {
            return "/tenants/" . $this->tenantIdent . "/" . $this->modelBasePath;
        }

        public function getIterator(): \Iterator
        {
            return new \ArrayIterator($this->domainsCollection);
        }

        /**
         * Obtain a complete collection of all domain objects associated to the Tenant
         *
         * @return CloudonixCollection
         */
        public function list(): CloudonixCollection
        {
            if (!isset($this->domainsCollection)) {
                $this->refreshDomainsCollection();
            }
            return $this->domainsCollection;
        }

        /**
         * Obtain information for a specific domain, based upon the domain name or numerical ID
         *
         * @param string $domain
         *
         * @return Domains
         */
        public function getDomain(string $domain): CloudonixDomain
        {
            $domainIdent = strtolower($domain);

            $rawData = $this->client->httpConnector->request('GET', $this->getPath() . "/" . $domain);
            return new CloudonixDomain($rawData, $this->client);
        }

        /**
         * Obtain the Cloudonix Application data, associated with a specific domain
         *
         * @param string $domainIdent
         *
         * @return object
         */
        public function getDomainApplication(string $domainIdent): object
        {
            $domainIdent = strtolower($domainIdent);
            return $this->domainsCollection->$domainIdent->application;
        }

        /**
         * Obtain the Cloudonix Profile data, associated with a specific domain
         *
         * @param string $domainIdent
         *
         * @return stdClass
         */
        public function getDomainProfile(string $domainIdent): stdClass
        {
            $domainIdent = strtolower($domainIdent);
            return $this->domainsCollection->$domainIdent->profile;
        }

        /**
         * Create a new domain and load it to the domains collection object
         *
         * @param string $domain
         *
         * @return CloudonixDomain
         */
        public function createDomain(string $domain): CloudonixDomain
        {
            $domain = strtolower($domain);
            if (get_called_class() != "Cloudonix\DataModel\Domains") {
                $rawData = $this->client->httpConnector->request('POST', $this->getModelPath(), ["domain" => $domain]);
            } else {
                $rawData = $this->client->httpConnector->request('POST', $this->getPath(), ["domain" => $domain]);
            }
            return new CloudonixDomain($rawData);
        }

        public function __set(string $name, mixed $value): void
        {
            return;
        }

        public function __get(string $name): mixed
        {
            return $this->$name;
        }

        public function __toString(): string
        {
            return json_encode($this);
        }

        private function refreshDomainsCollection(mixed $rawData = null): void
        {
            if (is_null($rawData)) {
                if (get_called_class() != "Cloudonix\DataModel\Domains") {
                    $rawData = $this->client->httpConnector->request('GET', $this->getModelPath());
                } else {
                    $rawData = $this->client->httpConnector->request('GET', $this->getPath());
                }
            }

            if (!isset($this->domainsCollection)) {
                $this->domainsCollection = new CloudonixCollection();
            }

            foreach ($rawData as $key => $value) {
                $tmpObject = new CloudonixDomain($value, $this->client);
                $this->domainsCollection[] = $tmpObject;
                unset($tmpObject);
            }
        }

        private function getModelPath(): string
        {
            return "/tenants/" . $this->tenantIdent . "/" . $this->modelBasePath;
        }

    }