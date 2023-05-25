<?php

    namespace Cloudonix\Entities;

    use Cloudonix\CXClient as CXClient;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Domain as EntityDomain;
    use Cloudonix\Entities\ContainerApplication as EntityContainerApplication;

    use Cloudonix\Collections\Domains as CollectionDomains;
    use Cloudonix\Collections\ContainerApplications as CollectionContainerApplications;

    use Exception;
    use Ramsey\Collection\Collection;

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
     * Tenant Data Model Entity
     * This class represents the generalised for of a Cloudonix Tenant object.
     *
     * @package cloudonix-php
     * @file    Entities/Tenant.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=tenants
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     *
     * @property-read int           $id         Tenant Numeric ID
     * @property-read string        $name       Tenant Name
     * @property      bool          $active     Tenant Status
     * @property-read string        $createdAt  Tenant Creation Date and time
     * @property-read string        $modifiedAt Tenant Last Modification Date and time
     * @property      EntityProfile $profile    Tenant Profile Object
     */
    class Tenant extends CloudonixEntity
    {
        protected CXClient $client;
        protected string $canonicalPath = "";
        protected string $entityId;

        public CollectionDomains $collectionDomains;
        public CollectionContainerApplications $collectionContainerApplications;

        /**
         * Tenant DataModel Object Constructor
         *
         * @param CXClient $client A CloudonixClient HTTP Connector Object
         */
        public function __construct(CXClient $client, string $entityId = "self")
        {
            $this->entityId = $entityId;
            $this->client = $client;
            $this->setPath($entityId);
            $this->refresh();
            parent::__construct($this);
        }

        /**
         * Return the entity REST API canonical path
         *
         * @return string
         */
        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        /**
         * Set the entity REST API canonical path
         *
         * @return string
         */
        protected function setPath(string $entityId): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = URLPATH_TENANTS . "/" . $entityId;
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
         * @throws Exception
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
         * @return Domain
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

            return $this->collectionDomains;
        }

        /**
         * Create a new Domain in the current Tenant and return the Domain's object
         *
         * @param string $domain
         *
         * @return Domain
         * @throws Exception
         */
        public function newDomain(string $domain): EntityDomain
        {
            $canonicalPath = $this->getPath() . URLPATH_DOMAINS;
            $newDomain = $this->client->httpConnector->request('POST', $canonicalPath, ['domain' => $domain]);
            return new EntityDomain($domain, $this, $newDomain);
        }

        public function containerApplications(): CollectionContainerApplications
        {
            if (!isset($this->collectionContainerApplications))
                $this->collectionContainerApplications = new CollectionContainerApplications($this);

            return $this->collectionContainerApplications;
        }

        public function containerApplication(string $containerApplicationName): EntityContainerApplication
        {
            return new EntityContainerApplication($containerApplicationName, $this);
        }

        /**
         * Build the tenant object properties
         *
         * @param mixed $input
         *
         * @return void
         */
        protected function buildEntityData(mixed $input): void
        {
            foreach ($input as $key => $value) {
                if (!is_object($value)) {
                    $this->$key = $value;
                } else {
                    if ($key == "profile") {
                        $this->profile = new EntityProfile($value, $this);
                    } else {
                        $this->$key = $value;
                    }
                }
            }
        }

        /**
         * Query the remote REST API for the latest tenant object
         *
         * @return string
         */
        protected function refresh(): mixed
        {
            return $this->client->httpConnector->request("GET", $this->getPath());
        }
    }