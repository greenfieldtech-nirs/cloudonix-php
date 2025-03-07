<?php
    /**
     * @package cloudonix-php
     * @file    Entities/Trunk.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=voice-trunk
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;

    /**
     * Trunk Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Trunk object.
     *
     * @property-read   int           $id                   Trunk Numeric ID
     * @property-read   int           $domainId             Domain Numeric ID
     * @property-read   int           $tenantId             Tenant Numeric ID
     * @property-read   int           $metric               Trunk Metric (used for outbound dialing trunk hunting)
     * @property-read   string        $direction            Trunk Direction
     *                                                      (public-inbound/public-outbound/outbound/inbound)
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
        protected mixed $client;
        protected string $canonicalPath;

        /**
         * Trunk DataModel Object Constructor
         *
         * @param string  $trunk                      Cloudonix Trunk Identifier
         * @param Domain  $parent                     The parent object that created this object
         * @param ?object $inputObject                A Cloudonix Trunk Object as stdClass
         *                                            If $trunkObject is provided, it will be used to build the Domain
         *                                            Entity object
         */
        public function __construct(string $trunk, Domain $parent, ?object $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($this, $parent);
            if (!is_null($inputObject)) {
                $this->setPath($inputObject->id, $parent->getPath());
                $this->buildEntityData($inputObject);
            } else {
                $this->setPath($trunk, $parent->getPath());
            }
        }

        /**
         * Update a trunks IP endpoint information
         *
         * @param string $ip
         * @param int    $port
         * @param string $prefix
         * @param string $transport
         *
         * @return $this
         */
        public function setEndpoint(string $ip, int $port = 5060, string $prefix = "", string $transport = ""): self
        {
            $port = (($port == 5060) && (isset($this->port))) ? $this->port : $port;
            $prefix = ((strlen($prefix) == 0) && (isset($this->prefix))) ? $this->prefix : $prefix;
            $transport = ((strlen($transport) == 0) && (isset($this->transport))) ? $this->transport : $transport;

            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'ip' => $ip,
                'port' => $port,
                'prefix' => $prefix,
                'transport' => $transport
            ]);
            $this->buildEntityData($result);
            return $this;
        }

        /**
         * Update an inbound Trunk IP Header Matching rule
         *
         * @param string $headerName
         * @param string $headerValue
         *
         * @return $this
         */
        public function setInboundFilter(string $headerName, string $headerValue): self
        {
            // If the trunk type isn't inbound, applying filters is not available
            if (($this->direction != "inbound") && ($this->direction != "public-inbound"))
                return $this;

            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'headerName' => $headerName,
                'headerValue' => $headerValue
            ]);
            $this->buildEntityData($result);
            return $this;
        }

        /**
         * Set an outbound Trunk Fixed Border profile
         *
         * @param string $border
         *
         * @return $this
         */
        public function setOutboundFixedBorder(string $border): self
        {
            if (($this->direction != "outbound") && ($this->direction != "public-outbound"))
                return $this;

            $this->profile['hostname'] = $border;

            $this->refresh();
            return $this;
        }

        /**
         * Set an outbound trunk outbound domain name
         *
         * @param string $domain
         *
         * @return $this
         */
        public function setOutboundDomainName(string $domain): self
        {
            if (($this->direction != "outbound") && ($this->direction != "public-outbound"))
                return $this;

            $this->profile['domain'] = $domain;

            $this->refresh();
            return $this;
        }

        /**
         * Set and outbound trunk SIP Request URI domain name
         *
         * @param string $ruri
         *
         * @return $this
         */
        public function setOutboundRURI(string $ruri): self
        {
            if (($this->direction != "outbound") && ($this->direction != "public-outbound"))
                return $this;

            $this->profile['ruri-domain'] = $ruri;

            $this->refresh();
            return $this;
        }

        /**
         * Set the priority metric of an outbound trunk
         *
         * @param int $metric
         *
         * @return $this
         */
        public function setOutboundMetric(int $metric): self
        {
            if (($this->direction != "outbound") && ($this->direction != "public-outbound"))
                return $this;

            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'metric' => $metric
            ]);

            $this->buildEntityData($result);
            return $this;
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
            if (!isset($this->canonicalPath)) {
                $this->canonicalPath = $branchPath . URLPATH_TRUNKS . "/" . $string;
            }
        }

        protected function buildEntityData(object|array $input): void
        {
            foreach ($input as $key => $value) {
                if ($key == "profile") {
                    $this->profile = new EntityProfile($this, $value);
                } else if ($key == "domain") {
                    continue;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }