<?php
    /**
     * @package cloudonixPhp
     * @file    Collections/Dnids.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=dnids
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Dnid as EntityDnid;

    /**
     * DNIDs Collection
     *
     * @property-read int    $id                                  DNID Numeric ID
     * @property-read int    $domainId                            Domain Numeric ID
     * @property-read int    $applicationId                       Voice Application Numeric ID
     * @property-read string $applicationName                     Voice Application Name
     * @property-read int    $messagingApplicationId              Voice Application Numeric ID
     * @property-read string $messagingApplicationName            Voice Application Name
     * @property-read string $dnid                                DNID Expression
     * @property-read string $source                              DNID Source String
     * @property-read bool   $expression                          DNID Source is RegEx formatted
     * @property-read bool   $prefix                              DNID Source is Prefix formatted
     * @property-read bool   $asteriskCompatible                  DNID Source is Asterisk extensions.conf formatted
     * @property-read bool   $global                              DNID is defined as global platform DNID
     * @property      bool   $active                              DNID Status
     * @property-read string $createdAt                           DNID Creation Date and time
     * @property-read string $modifiedAt                          DNID Last Modification Date and time
     * @property-read string $deletedAt                           DNID Deletion Date and time
     */
    class Dnids extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        public mixed $client;
        public string $canonicalPath = "";
        private mixed $parent;

        public function __construct(mixed $parent)
        {
            $this->client = $parent->client;
            $this->parent = $parent;
            $this->setPath($parent->canonicalPath);
            parent::__construct();
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
                $this->canonicalPath = $branchPath . URLPATH_DNIDS;
        }

        /**
         * Refresh the collection
         *
         * @return $this
         */
        public function refresh(): Dnids
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
                    $this->collection[$value->id] = new EntityDnid($value->id, $this->parent, $value);
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