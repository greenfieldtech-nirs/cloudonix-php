<?php

    namespace Cloudonix\Entities;

    use Cloudonix\CXClient as CloudonixClient;
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
     * @property-read   int           $id                   Trunk Numeric ID
     * @property-read   int           $domainId             Domain Numeric ID
     * @property-read   int           $tenantId             Tenant Numeric ID
     * @property-read   int           $metric               Trunk Metric (used for outbound dialing trunk hunting)
     * @property-read   string        $direction            Trunk Direction
     *                  (public-inbound/public-outbound/outbound/inbound)
     * @property        string        $prefix               Trunk Technical Prefix for dialled numbers
     * @property-read   string        $name                 Trunk Name
     * @property        EntityProfile $profile              Trunk Profile
     * @property        string        $headerName           Trunk SIP-Header Matching
     * @property        string        $headerValue          Trunk SIP-Header Matching Value Expression
     * @property        string        $transport            Trunk Transport (UDP/TCP/TLS)
     * @property        string        $ip                   Trunk IP Address or FQDN
     * @property        int           $port                 Trunk IP Port (default: 5060)
     * @property        bool          $active               Trunk Status
     */
    class Trunk extends CloudonixEntity
    {
        protected CloudonixClient $client;

        /**
         * Trunk DataModel Object Constructor
         *
         * @param string      $trunk                  Cloudonix Trunk Identifier
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $trunkObject           A Cloudonix Trunk Object as stdClass
         *                                            If $trunkObject is provided, it will be used to build the Domain
         *                                            Entity object
         */
        public function __construct(string $trunk, mixed $parentBranch, object $trunkObject = null)
        {
            $this->client = $parentBranch->client;
            parent::__construct($this->client);
            if (!is_null($trunkObject)) {
                $this->buildEntityData($trunkObject);
                $this->setPath($trunkObject->domain, $parentBranch->canonicalPath);
            } else {
                $this->setPath($trunk, $parentBranch->canonicalPath);
            }
        }

        public function setEndpoint(string $ip, int $port = 5060, $prefix = ""): Trunk
        {
            return $this;
        }

        public function setInboundFilter(string $ip, int $port = 5060, $prefix = ""): Trunk
        {
            return $this;
        }

        public function setOutboundFixedBorder(string $border): Trunk
        {

        }

        public function setOutboundDomainName(string $domain): Trunk
        {

        }

        public function setOutboundRURI(string $ruri): Trunk
        {

        }
        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            if ($result->code == 204)
                return true;
            return false;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_TRUNKS . "/" . $string;
        }

        public function refresh(): Domain
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        public function __toString(): string
        {
            return json_encode($this->refresh());
        }

        protected function buildEntityData(mixed $input): void
        {
            foreach ($input as $key => $value) {
                if ($key == "profile") {
                    $this->profile = new EntityProfile($value, $this);
                } else if ($key == "domain") {
                    continue;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }