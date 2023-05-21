<?php

    namespace Cloudonix\Entities;

    use Cloudonix\CXClient as CXClient;
    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;

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
            $this->refreshData();
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
            $this->createTenantObjectResponse($this->refreshData());
            return $this;
        }

        /**
         * Enable/disable the Cloudonix Tenant account
         *
         * @param bool $active
         *
         * @return $this
         * @throws \Exception
         */
        public function setActive(bool $active = true): Tenant
        {
            $this->client->httpConnector->request("PATCH", $this->getPath(), ['active' => $active]);
            return $this->get();
        }

        /**
         * Build the tenant object properties
         *
         * @param mixed $input
         *
         * @return void
         */
        private function createTenantObjectResponse(mixed $input)
        {
            foreach ($input as $key => $value) {
                if (!is_object($value)) {
                    $this->$key = $value;
                } else {
                    if ($key == "profile") {
                        $this->profile = new Profile($value, $this);
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
        private function refreshData(): mixed
        {
            return $this->client->httpConnector->request("GET", $this->getPath());
        }
    }