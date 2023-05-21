<?php

    namespace Cloudonix\Entities;

    use Cloudonix\Client as CloudonixClient;
    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Tenant as EntityTenant;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Application as EntityApplication;

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
     * @property-read int           $id                     Domain Numeric ID
     * @property-read int           $tenantId               Tenant Numeric ID
     * @property      string        $domain                 Domain name, usually an FQDN
     * @property      bool          $active                 Domain Status
     * @property-read string        $createdAt              Domain Creation Date and time
     * @property-read string        $modifiedAt             Domain Last Modification Date and time
     * @property-read string        $deletedAt              Domain Deletion Date and time
     * @property      bool          $registrationFree       Domain RegFree Dialing Status
     * @property      int           $defaultApplication     Domain Default Application ID
     * @property-read string        $uuid                   Domain UUID
     * @property      EntityProfile $profile                Tenant Profile Object
     */
    class Domain extends CloudonixEntity
    {
        protected EntityApplication $application;
        protected EntityTenant $tenant;
        protected CloudonixClient $client;

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
                $this->application = new EntityApplication($this->application, $client);
            }
            if (isset($this->tenant)) {
                $this->tenant = new EntityTenant($this->tenant, $client);
            }
        }

        public function getPath()
        {
            // TODO: Implement getPath() method.
        }
    }