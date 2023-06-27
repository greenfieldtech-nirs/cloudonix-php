<?php
    /**
     * @package cloudonix-php
     * @file    Collections/HostedApplications.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=hosted-applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use GuzzleHttp\Exception\GuzzleException;

    use Cloudonix\CloudonixClient;
    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\HostedApplication as EntityHostedApplication;
    use Cloudonix\Entities\CloudonixEntity;
    use Cloudonix\Entities\Tenant;

    /**
     * HostedApplications Collection
     */
    class HostedApplications extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        protected CloudonixClient $client;
        protected CloudonixEntity $parent;
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
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_CONTAINER_APPLICATIONS;
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
                $this->collection[] = new EntityHostedApplication($value->name, $this->parent, $value);
            }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }

        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $this->collection[$offset]->name);
            if ($result->code == 204) {
                parent::offsetUnset($offset);
            }
        }
    }