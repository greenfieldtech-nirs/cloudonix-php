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
     * Application Data Model Entity
     * This class represents the generalised form of a Cloudonix Application object.
     *
     * @package cloudonixPhp
     * @filename: Entities/Application.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     *
     * @property-read int              $id                     Application Numeric ID
     * @property-read int              $domainId               Domain Numeric ID
     * @property-read string           $name                   Application Name
     * @property      bool             $active                 Application Status
     * @property-read string           $createdAt              Application Creation Date and time
     * @property-read string           $modifiedAt             Application Last Modification Date and time
     * @property-read string           $deletedAt              Application Deletion Date and time
     * @property-read string           $type                   Application type
     * @property      string           $url                    Application URL
     * @property      string           $method                 Application Method (GET or POST)
     * @property      CloudonixProfile $profile                Application Profile Object
     */
    class Application extends CloudonixEntity
    {

        private string $modelBasePath = "applications";

        public function __construct(mixed $stdObject)
        {
            parent::__construct($stdObject);
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

    }