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

    use Cloudonix\Entities\Apikey as EntityApikey;
    use Traversable;
    use ArrayIterator;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;

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
     * VoiceApplications Collection
     *
     * @package cloudonixPhp
     * @file    Collections/Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=keys
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     *
     * @property-read int    $id                     Apikey Numeric ID
     * @property-read string $name                   Apikey Name
     * @property-read int    $tenantId               Tenant Numeric ID
     * @property-read int    $domainId               Domain Numeric ID
     * @property-read int    $applicationId          Application Numeric ID
     * @property-read int    $subscriberId           Subscriber Numeric ID
     * @property-read string $userId                 Cockpit User ID
     * @property      bool   $active                 Application Status
     * @property-read string $keyId                  API Key String
     * @property-read string $secret                 API Key Secret
     * @property-read string $type                   API Key Type (Informational Only)
     * @property-read string $accessRights           API Key Access Rights
     * @property-read string $createdAt              API Key Creation Date and time
     * @property-read string $modifiedAt             API Key Last Modification Date and time
     * @property-read string $deletedAt              API Key Deletion Date and time
     */
    class Apikeys extends CloudonixCollection
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        protected mixed $parent;

        public function __construct(mixed $parent)
        {
            $this->client = $parent->client;
            $this->parent = $parent;
            $this->setPath($parent->canonicalPath);
            $this->refresh();
            parent::__construct();
        }

        /**
         * Create a new API Key in the current Access Right (based upon the parent class)
         *
         * @param string $name
         *
         * @return EntityApikey
         */
        public function create(string $name): EntityApikey
        {
            $newKey = $this->client->httpConnector->request("POST", $this->getPath(), ['name' => $name]);
            $this->collection[$newKey->keyId] = new EntityApikey($newKey->keyId, $this->parent, $newKey);
            $this->collectionCount++;
            return $this->collection[$newKey->keyId];
        }

        public function list(): Apikeys
        {
            return $this;
        }

        public function newKey(string $keyname): EntityApikey
        {
            $newApikeyObject = $this->client->httpConnector->request("POST", $this->getPath(), [ "name" => $keyname ]);
            return new EntityApikey($newApikeyObject->keyId, $this->parent, $newApikeyObject);
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
                $this->canonicalPath = $branchPath . URLPATH_APIKEYS;
        }

        /**
         * Refresh the collection data from remote API
         *
         * @return $this
         */
        public function refresh(): Apikeys
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local collection data storage
         *
         * @param mixed $param
         *
         * @return array
         */
        protected function refreshCollectionData(mixed $param): array
        {
            $this->collection = [];
            if (!is_null($param))
                foreach ($param as $key => $value) {
                    $this->collection[$value->keyId] = new EntityApikey($value->keyId, $this->parent, $value);
                    $this->collectionCount++;
                }
            return $this->collection;
        }

        /**
         * Delete the remote API key when a collection member is unset
         *
         * @param mixed $offset
         *
         * @return void
         */
        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $offset);
            if (!isset($result->code)) {
                unset($this->collection[$offset]);
                $this->collectionCount++;
            }
        }

        /*
        public function offsetSet(mixed $offset, mixed $value): void
        {
            if (is_null($offset)) {
                $this->collection[] = $value;
            } else {
                $this->collection[$offset] = $value;
            }
        }

        public function offsetGet(mixed $offset): mixed
        {
            $this->refresh();
            return parent::offsetGet($offset);
        }
        */

        public function getIterator(): Traversable
        {
            return parent::getIterator();
        }

        public function __toString(): string
        {
            return parent::__toString();
        }
    }