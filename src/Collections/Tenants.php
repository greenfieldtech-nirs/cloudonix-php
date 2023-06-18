<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Dnids.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=tenants
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Tenant as EntityTenant;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;

    /**
     * Tenants Collection
     *
     * @property-read int           $id                                  Tenant Numeric ID
     * @property      bool          $active                              Tenant Status
     * @property-read string        $name                                Tenant Name
     * @property      EntityProfile $profile                             Tenant Profile Object
     * @property-read string        $createdAt                           Tenant Creation Date and time
     * @property-read string        $modifiedAt                          Tenant Last Modification Date and time
     * @property-read string        $lastActivity                        Tenant Last Time the tenant interacted with
     *                                                                   the platform
     */
    class Tenants extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        public mixed $client;
        public string $canonicalPath = "";
        private mixed $parent;

        public function __construct(mixed $parent)
        {
            $this->client = $parent->client;
            $this->parent = $parent;
            $this->setPath($parent->canonicalPath);
            parent::__construct($this);
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
                $this->canonicalPath = $branchPath . URLPATH_TENANTS;
        }

        /**
         * Refresh the collection
         *
         * @return $this
         */
        public function refresh(): Tenants
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
                    $this->collection[$value->id] = new EntityTenant($value->id, $this->parent, $value);
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