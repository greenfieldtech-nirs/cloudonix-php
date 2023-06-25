<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Dnids.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=trunks
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Profile as EntityProfile;

    use Cloudonix\Entities\Domain;

    /**
     * Trunks Collection
     *
     * @property-read   int           $id                   Trunk Numeric ID
     * @property-read   int           $domainId             Domain Numeric ID
     * @property-read   int           $tenantId             Tenant Numeric ID
     * @property-read   int           $metric               Trunk Metric (used for outbound dialing trunk hunting)
     * @property-read   string        $direction            Trunk Direction (public-inbound/public-outbound)
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
    class Trunks extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected mixed $parent;
        protected string $canonicalPath;

        public function __construct(string $trunk, Domain $parentBranch, object $trunkObject = null)
        {
            $this->client = $parentBranch->getClient();
            parent::__construct($this);
            if (!is_null($trunkObject)) {
                $this->buildEntityData($trunkObject);
                $this->setPath($trunkObject->domain, $parentBranch->getPath());
            } else {
                $this->setPath($trunk, $parentBranch->getPath());
            }
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
         * @param string $branchPath
         *
         * @return void
         */
        protected function setPath(string $branchPath): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_APPLICATIONS;
        }

        /**
         * Refresh the collection
         *
         * @return $this
         */
        public function refresh(): Trunks
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local collection data storage
         *
         * @param mixed $param
         *
         * @return void
         */
        protected function refreshCollectionData(mixed $param): array
        {
            $this->collection = [];
            if (!is_null($param))
                foreach ($param as $key => $value) {
                    $this->collection[$value->name] = new EntityApplication($value->name, $this->parent, $value);
                }
            return $this->collection;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }

        public function offsetUnset(mixed $offset): void
        {
            return;
        }

        public function offsetGet(mixed $offset): mixed
        {
            if (!count($this->collection)) $this->refresh();
            return parent::offsetGet($offset);
        }

        public function getIterator(): Traversable
        {
            if (!count($this->collection)) $this->refresh();
            return parent::getIterator();
        }

        public function __toString(): string
        {
            if (!count($this->collection)) $this->refresh();
            return parent::__toString();
        }
    }