<?php
    /**
     * @package cloudonix-php
     * @file    Entities/Tenant.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\CloudonixClient;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Domain as EntityDomain;
    use Cloudonix\Entities\ContainerApplication as EntityContainerApplication;
    use Cloudonix\Entities\Apikey as EntityApikey;

    use Cloudonix\Collections\Domains as CollectionDomains;
    use Cloudonix\Collections\ContainerApplications as CollectionContainerApplications;
    use Cloudonix\Collections\Apikeys as CollectionApikeys;

    use Cloudonix\Helpers\UtilityHelper as UtilityHelper;

    use Exception;
    use GuzzleHttp\Exception\GuzzleException;

    /**
     * Tenant Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Tenant object.
     *
     * @property-read int           $id                                  Tenant Numeric ID
     * @property      bool          $active                              Tenant Status
     * @property-read string        $name                                Tenant Name
     * @property      EntityProfile $profile                             Tenant Profile Object
     * @property-read string        $createdAt                           Tenant Creation Date and time
     * @property-read string        $modifiedAt                          Tenant Last Modification Date and time
     * @property-read string        $lastActivity                        Tenant Last Time the tenant interacted with
     */
    class Tenant extends CloudonixEntity
    {
        protected mixed $client;
        protected string $parentBranch;
        protected string $canonicalPath;
        protected string $entityId;

        public CollectionDomains $collectionDomains;
        public CollectionContainerApplications $collectionContainerApplications;
        public CollectionApikeys $collectionApikeys;

        /**
         * Tenant DataModel Object Constructor
         *
         * @param CloudonixClient $client A CloudonixClient HTTP Connector Object
         */
        public function __construct(CloudonixClient $client, string $entityId = "self")
        {
            $this->entityId = $entityId;
            $this->client = $client;
            $this->setPath($entityId);
            parent::__construct($this);
        }

        /**
         * Get the Cloudonix Tenant object
         *
         * @return $this
         */
        public function get(): Tenant
        {
            $this->buildEntityData($this->refresh());
            return $this;
        }

        /**
         * Enable/disable the Cloudonix Tenant account
         *
         * @param bool $active
         *
         * @return $this
         * @throws GuzzleException
         */
        public function setActive(bool $active = true): Tenant
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['active' => $active]);
            return $this->get();
        }

        /**
         * Obtain a Cloudonix Domain object based upon a provided domain name or domain ID
         *
         * @param string $domain Domain name or ID
         *
         * @return EntityDomain
         */
        public function domain(string $domain): EntityDomain
        {
            return new EntityDomain($domain, $this);
        }

        /**
         * Obtain a Cloudonix Domains Collection object
         *
         * @return CollectionDomains
         */
        public function domains(): CollectionDomains
        {
            if (!isset($this->collectionDomains))
                $this->collectionDomains = new CollectionDomains($this);

            return $this->collectionDomains->refresh();
        }

        /**
         * Create a new Domain in the current Tenant and return the Domain's object
         *
         * @param string $domain
         *
         * @return EntityDomain
         * @throws GuzzleException
         */
        public function newDomain(string $domain): EntityDomain
        {
            $canonicalPath = $this->getPath() . URLPATH_DOMAINS;
            $result = $this->client->httpConnector->request('POST', $canonicalPath, ['domain' => $domain]);
            return new EntityDomain($domain, $this, $result);
        }

        /**
         * Obtain a collection of all tenant associated container applications
         *
         * @return CollectionContainerApplications
         */
        public function containerApplications(): CollectionContainerApplications
        {
            if (!isset($this->collectionContainerApplications))
                $this->collectionContainerApplications = new CollectionContainerApplications($this);

            return $this->collectionContainerApplications->refresh();
        }

        /**
         * Obtain a container application object, based upon the container application name
         *
         * @param string $name
         *
         * @return ContainerApplication
         */
        public function containerApplication(string $name): EntityContainerApplication
        {
            return new EntityContainerApplication($name, $this);
        }

        /**
         * Create a new container application
         *
         * @param string $name
         * @param string $runtime
         * @param string $code
         *
         * @return ContainerApplication
         */
        public function newContainerApplication(string $name, string $runtime, string $code): EntityContainerApplication
        {
            $utilityHelper = new UtilityHelper();
            $canonicalPath = $this->getPath() . URLPATH_CONTAINER_APPLICATIONS;
            $result = $this->client->httpConnector->request('POST', $canonicalPath, [
                'name' => $name,
                'blocks' => [
                    [
                        'name' => 'main',
                        'runtime' => $runtime,
                        'code' => $utilityHelper->validateCode($code)
                    ]
                ]
            ]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " newContainerApplication API Response: " . json_encode($result));
            return new EntityContainerApplication($name, $this, $result);
        }

        /**
         * Obtain a collection of API Keys associated with the tenant
         *
         * @return CollectionApikeys
         */
        public function apikeys(): CollectionApikeys
        {
            if (!isset($this->collectionApikeys))
                $this->collectionApikeys = new CollectionApikeys($this);

            return $this->collectionApikeys->refresh();
        }

        /**
         * Obtain an API key object, based upon the API key ID
         *
         * @param string $keyId
         *
         * @return Apikey
         */
        public function apikey(string $keyId): EntityApikey
        {
            return new EntityApikey($keyId, $this);
        }

        /**
         * Create a new API key and return its object
         *
         * @param string $keyName
         *
         * @return Apikey
         */
        public function newApikey(string $keyName): EntityApikey
        {
            return $this->collectionApikeys->newKey($keyName);
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $entityId): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = URLPATH_TENANTS . "/" . $entityId;
        }

        private function buildEntityData(mixed $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            foreach ($input as $key => $value) {
                if (!is_object($value)) {
                    $this->$key = $value;
                } else {
                    if ($key == "profile") {
                        $this->profile = new EntityProfile($value, $this);
                    } else if ($key == "settings") {
                        continue;
                    } else {
                        $this->$key = $value;
                    }
                }
            }
        }

        protected function refresh(): mixed
        {
            return $this->client->httpConnector->request("GET", $this->getPath());
        }
    }