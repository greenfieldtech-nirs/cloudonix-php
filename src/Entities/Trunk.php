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
     * Trunk Data Model Entity
     * This class represents the generalised form of a Cloudonix Trunk object.
     * Profiles are used in Cloudonix with various data models - and is free formed.
     *
     * @package cloudonixPhp
     * @file    Entities/Subscriber.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=voice-trunk
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     *
     * @property-read int           $id                        Trunk Numeric ID
     * @property-read int           $domainId                  Domain Numeric ID
     * @property-read string        $name                      Trunk Name
     * @property      bool          $active                    Subscriber Status
     * @property      string        $ip                        Trunk IP/FQDN Address
     * @property      int           $port                      Trunk Port
     * @property      string        $transport                 Trunk Transport (UDP/TCP/TLS)
     * @property      string        $prefix                    Trunk Dialing/Routing Prefix
     * @property      string        $direction                 Trunk Direction
     *                (public-outbound/public-inbound/outbound/inbound)
     * @property      int           $metric                    Trunk Routing Preference Metric
     * @property-read string        $createdAt                 Subscriber Creation Date and time
     * @property-read string        $modifiedAt                Subscriber Last Modification Date and time
     * @property-read string        $deletedAt                 Subscriber Deletion Date and time
     * @property      EntityProfile $profile                   Subscriber Profile Object
     */
    class Trunk extends CloudonixEntity
    {
        protected CloudonixClient $client;

        /**
         * Trunk DataModel Object Constructor
         *
         * @param CloudonixClient $client    A CloudonixClient HTTP Connector Object
         * @param string          $stdObject A CloudonixDomain Object
         */
        public function __construct(mixed $stdObject, CloudonixClient $client)
        {
            parent::__construct($stdObject);
            $this->client = $client;
        }

    }