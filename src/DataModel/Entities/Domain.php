<?php

    namespace Cloudonix\DataModel\Entities;

    use Cloudonix\Client as CloudonixClient;
    use Cloudonix\DataModel\Commons\CloudonixCollection as CloudonixCollection;
    use Cloudonix\DataModel\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\DataModel\Entities\Tenant as CloudonixTenant;
    use Cloudonix\DataModel\Entities\Profile as CloudonixProfile;
    use Cloudonix\DataModel\Entities\Application as CloudonixApplication;

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
     * Domain Data Model Entity
     * This class represents the generalised form of a Cloudonix Domain object.
     *
     * @package  cloudonixPhp
     * @filename Entities/Domain.php
     * @author   Nir Simionovich <nirs@cloudonix.io>
     * @see      https://dev.docs.cloudonix.io/#/platform/api-core/models?id=domains
     * @license  MIT License (https://choosealicense.com/licenses/mit/)
     * @created  2023-05-14
     *
     * @property-read int              $id                     Domain Numeric ID
     * @property-read int              $tenantId               Tenant Numeric ID
     * @property      string           $domain                 Domain name, usually an FQDN
     * @property      bool             $active                 Domain Status
     * @property-read string           $createdAt              Domain Creation Date and time
     * @property-read string           $modifiedAt             Domain Last Modification Date and time
     * @property-read string           $deletedAt              Domain Deletion Date and time
     * @property      bool             $registrationFree       Domain RegFree Dialing Status
     * @property      int              $defaultApplication     Domain Default Application ID
     * @property-read string           $uuid                   Domain UUID
     * @property      CloudonixProfile $profile                Tenant Profile Object
     */
    class Domain extends CloudonixEntity
    {

        /**
         * @var string The Cloudonix REST API Canonical path
         */
        private string $modelBasePath = "domains";

        /**
         * Domain DataModel Object Constructor
         *
         * @param CloudonixClient $client    A CloudonixClient HTTP Connector Object
         * @param string          $stdObject A CloudonixDomain Object
         */
        public function __construct(mixed $stdObject, CloudonixClient $client)
        {
            parent::__construct($stdObject);

            $this->client = $client;
            if (isset($this->application)) {
                $this->application = new CloudonixApplication($this->application);
            }
            if (isset($this->tenant)) {
                $this->tenant = new CloudonixTenant($this->tenant);
            }

        }

        public function getApplications(): CloudonixCollection
        {
            $applicationsCollection = [];
            $applicationList = $this->client->httpConnector->request('GET', $this->getPath());
            if (!is_iterable($applicationList))
                $applicationsCollection[] = $applicationList;

            return new CloudonixCollection($applicationsCollection);
        }

        public function getApplication(mixed $application): CloudonixApplication
        {
            $applicationObject = $this->client->httpConnector->request('GET', $this->getPath() . "/applications/" . $application);
            return new CloudonixApplication($applicationObject);
        }

        /**
         * Return the data model associated canonical path
         *
         * @return string
         */
        public function getPath(): string
        {
            return "/tenants/" . $this->tenantId . "/" . $this->modelBasePath . "/" . $this->domain;
        }

        public function __get(mixed $name)
        {
            return $this->$name;
        }

        public function __set(string $name, mixed $value)
        {
            $this->$name = $value;
        }
    }