<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Dnids.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=dnids
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use Cloudonix\Entities\CloudonixEntity;
    use Cloudonix\Entities\Dnid as EntityDnid;

    /**
     * DNIDs Collection
     *
     * @see \Cloudonix\Entities\Dnid     For more information about DNID Data Model
     */
    class Dnids extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected CloudonixEntity|CloudonixCollection $parent;
        protected string $canonicalPath;

        public function __construct(CloudonixEntity $parent)
        {
            $this->client = $parent->getClient();
            $this->parent = $parent;
            $this->setPath($parent->getPath());
            parent::__construct($this);
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $branchPath): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_DNIDS;
        }

        public function refresh(): Dnids
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function refreshCollectionData(object|array $param): array
        {
            $this->collection = [];
            foreach ($param as $key => $value) {
                $this->collection[] = new EntityDnid($value->id, $this->parent, $value);
            }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $this->collection[$offset]->id);
            if ($result->code == 204) {
                parent::offsetUnset($offset);
            }
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }
    }