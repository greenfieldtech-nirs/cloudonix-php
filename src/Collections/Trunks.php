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

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Trunk as EntityTrunk;
    use Cloudonix\Entities\Domain;

    /**
     * Trunks Collection
     *
     * @see \Cloudonix\Entities\Trunk     For more information about Trunk Data Model
     */
    class Trunks extends CloudonixCollection
    {
        protected mixed $client;
        protected Domain $parent;
        protected string $canonicalPath;

        public function __construct(Domain $parent)
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
                $this->canonicalPath = $branchPath . URLPATH_TRUNKS;
        }

        public function refresh(): self
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function refreshCollectionData(object|array $param): array
        {
            $this->collection = [];
            foreach ($param as $value) {
                $this->collection[] = new EntityTrunk($value->name, $this->parent, $value);
            }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $this->collection[$offset]->name);
            if ($result->code == 204) {
                parent::offsetUnset($offset);
            }
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }
    }