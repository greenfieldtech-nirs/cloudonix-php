<?php

    namespace Cloudonix\DataModel\Entities;

    use Cloudonix\DataModel\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\DataModel\Entities\Profile as CloudonixProfile;

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
     * @package cloudonixPhp
     * @file    Entities/Tenant.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=tenant
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     *
     * @property-read int              $id         Tenant Numeric ID
     * @property-read string           $name       Tenant Name
     * @property      bool             $active     Tenant Status
     * @property-read string           $createdAt  Tenant Creation Date and time
     * @property-read string           $modifiedAt Tenant Last Modification Date and time
     * @property      CloudonixProfile $profile    Tenant Profile Object
     */
    class Tenant extends CloudonixEntity
    {
        /**
         * @var string The Cloudonix REST API Canonical path
         */
        private string $modelBasePath = "tenants";

        public function __construct(mixed $stdObject)
        {
            parent::__construct($stdObject);
            if (isset($stdObject->profile)) {
                $this->profile = new CloudonixProfile($stdObject->profile);
            }
        }

        /**
         * Return the data model associated canonical path
         *
         * @return string
         */
        public function getPath(): string
        {
            return $this->modelBasePath . "/" . $this->name;
        }

        public function __get(mixed $name)
        {
            return $this->$name;
        }

    }