<?php
    /**
     * @package cloudonixPhp
     * @file    Collections/Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=domains
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\VoiceApplication as EntityApplication;
    use Cloudonix\Entities\Domain as EntityDomain;
    use Cloudonix\Entities\Profile as EntityProfile;

    /**
     * Domains Collection
     *
     * @property-read int               $id                         Domain Numeric ID
     * @property-read int               $tenantId                   Tenant Numeric ID
     * @property      string            $domain                     Domain name, usually an FQDN
     * @property      bool              $active                     Domain Status
     * @property-read string            $createdAt                  Domain Creation Date and time
     * @property-read string            $modifiedAt                 Domain Last Modification Date and time
     * @property-read string            $deletedAt                  Domain Deletion Date and time
     * @property      bool              $registrationFree           Domain RegFree Dialing Status
     * @property      int               $defaultApplication         Domain Default Application ID
     * @property-read string            $uuid                       Domain UUID
     * @property      EntityProfile     $profile                    Domain Profile Object
     * @property-read EntityApplication $application                Domain Default Application Object
     */
    class Domains extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
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
                $this->canonicalPath = $branchPath . URLPATH_DOMAINS;
        }

        public function refresh(): Domains
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
                    $this->collection[$value->domain] = new EntityDomain($value->domain, $this->parent, $value);
                }
            return $this->collection;
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