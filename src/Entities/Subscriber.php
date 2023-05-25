<?php
    /**
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     *
     * @project :  cloudonix-php
     * @filename: Domain.php
     * @author  :   nirs
     * @created :  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Client as CloudonixClient;
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
     * Subscriber Data Model Entity
     * This class represents the generalised form of a Cloudonix Subscriber object.
     * Profiles are used in Cloudonix with various data models - and is free formed.
     *
     * @package cloudonixPhp
     * @file    Entities/Subscriber.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=subscriber
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     *
     * @property-read int           $id                     Subscriber Numeric ID
     * @property-read int           $domainId               Domain Numeric ID
     * @property-read string        $msisdn                 Subscriber Phone/Login string, normally an E.164 Number
     * @property      bool          $active                 Subscriber Status
     * @property-read string        $createdAt              Subscriber Creation Date and time
     * @property-read string        $modifiedAt             Subscriber Last Modification Date and time
     * @property-read string        $deletedAt              Subscriber Deletion Date and time
     * @property      EntityProfile $profile                Subscriber Profile Object
     */
    class Subscriber extends CloudonixEntity
    {
        protected CloudonixClient $client;

        /**
         * Subscriber DataModel Object Constructor
         *
         * @param CloudonixClient $client    A CloudonixClient HTTP Connector Object
         * @param string          $stdObject A CloudonixDomain Object
         */
        public function __construct(mixed $stdObject, CloudonixClient $client)
        {
            parent::__construct($stdObject);
            $this->client = $client;
        }

        public function getPath()
        {
            // TODO: Implement getPath() method.
        }

        protected function buildEntityData(mixed $input)
        {
            // TODO: Implement buildEntityData() method.
        }

        protected function refresh()
        {
            // TODO: Implement refresh() method.
        }
    }