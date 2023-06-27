<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Apikeys.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=keys
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use GuzzleHttp\Exception\GuzzleException;

    use Cloudonix\Entities\Apikey as EntityApikey;
    use Cloudonix\Entities\CloudonixEntity;
    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;

    /**
     * API Keys Collection
     */
    class Apikeys extends CloudonixCollection
    {
        protected object $client;
        protected object $parent;
        protected string $canonicalPath;

        public function __construct(CloudonixEntity $parent)
        {
            $this->client = $parent->getClient();
            $this->parent = $parent;
            $this->setPath($parent->getPath());
            parent::__construct($this);
        }

        /**
         * Create a new API Key in the current Access Right (based upon the parent class)
         *
         * @param string $name
         *
         * @return EntityApikey
         * @throws GuzzleException
         */
        public function newKey(string $name): EntityApikey
        {
            $newApikeyObject = $this->client->httpConnector->request("POST", $this->getPath(), ["name" => $name]);
            $newApikey = new EntityApikey($newApikeyObject->keyId, $this->parent, $newApikeyObject);
            $this->collection[] = $newApikey;
            $this->collectionCount++;
            return $newApikey;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $branchPath): void
        {
            if (!isset($this->canonicalPath)) {
                $this->canonicalPath = $branchPath . URLPATH_APIKEYS;
            }
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
                $this->collection[] = new EntityApikey($value->keyId, $this->parent, $value);
            }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        /**
         * Delete the remote API key when a collection member is unset
         *
         * @param mixed $offset
         *
         * @return void
         * @throws GuzzleException
         */
        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $this->collection[$offset]->keyId);
            if ($result->code == 204) {
                parent::offsetUnset($offset);
            }

        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }
    }