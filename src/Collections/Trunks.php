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
     * @filename: Domains.php
     * @author  :   nirs
     * @created :  2023-05-15
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
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
     * Inbound Trunks Collection
     *
     * @package cloudonixPhp
     * @file    Collections/Dnids.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=trunks
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
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
        public mixed $client;
        public string $canonicalPath = "";
        private mixed $parent;

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
            if (!strlen($this->canonicalPath))
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
            $this->refresh();
            return parent::offsetGet($offset);
        }

        public function getIterator(): Traversable
        {
            $this->refresh();
            return parent::getIterator();
        }

        public function __toString(): string
        {
            $this->refresh();
            return parent::__toString();
        }
    }