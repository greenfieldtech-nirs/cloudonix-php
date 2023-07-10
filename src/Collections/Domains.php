<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=domains
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use Cloudonix\Entities\Tenant;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Domain as EntityDomain;

    /**
     * Domains Collection
     *
     * @see \Cloudonix\Entities\Domain     For more information about Domain Data Model
     */
    class Domains extends CloudonixCollection
    {
        protected mixed $client;
        protected mixed $parent;
        protected string $canonicalPath;

        public function __construct(Tenant $parent)
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
            if (!isset($this->canonicalPath)) {
                $this->canonicalPath = $branchPath . URLPATH_DOMAINS;
            }
        }

        protected function refresh(): self
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function refreshCollectionData(object|array $param): array
        {
            $this->collection = [];
            foreach ($param as $key => $value) {
                $this->collection[] = new EntityDomain($value->domain, $this->parent, $value);
            }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        public function offsetUnset(mixed $offset): void
        {
            $domain = $this->collection[$offset]->domain;
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $domain);
            if ($result->code == 204) {
                parent::offsetUnset($offset);
            }
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }
    }