<?php

    namespace Cloudonix\Entities;

    use Cloudonix\Client as CloudonixClient;
    use Cloudonix\DataModel\CloudonixEntity as CloudonixEntity;

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
     * API Key Data Model Entity
     * This class represents the generalised form of a Cloudonix Application object.
     *
     * @package cloudonixPhp
     * @filename: Entities/Application.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=apikeys
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
    class Apikey extends CloudonixEntity
    {
        protected CloudonixClient $client;

        /**
         * Domain DataModel Object Constructor
         *
         * @param CloudonixClient $client    A CloudonixClient HTTP Connector Object
         * @param string          $stdObject A CloudonixDomain Object
         */
        public function __construct(mixed $stdObject, CloudonixClient $client)        {
            parent::__construct($stdObject);
            $this->client = $client;
        }

    }