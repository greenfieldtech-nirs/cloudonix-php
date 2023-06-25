<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\VoiceApplication as EntityApplication;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Domain;

    /**
     * VoiceApplications Collection
     *
     * @property-read int     $id                         Voice Application Numeric ID
     * @property-read int     $domainId                   Domain Numeric ID
     * @property-read string  $name                       Voice Application Name
     * @property-read string  $type                       Voice Application Type
     * @property      string  $url                        Voice Application URL Endpoint
     * @property      string  $method                     Voice Application Endpoint HTTP Method
     * @property      bool    $active                     Voice Application Status
     * @property-read string  $createdAt                  Voice Application Creation Date and time
     * @property-read string  $modifiedAt                 Voice Application Last Modification Date and time
     * @property-read string  $deletedAt                  Voice Application Deletion Date and time
     * @property      EntityProfile $profile                    Voice Application Profile Object
     */
    class VoiceApplications extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected mixed $parent;
        protected string $canonicalPath;

        public function __construct(Domain $parent)
        {
            $this->client = $parent->getClient();
            $this->parent = $parent;
            $this->setPath($parent->getPath());
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
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_APPLICATIONS;
        }

        /**
         * Refresh the collection
         *
         * @return $this
         */
        public function refresh(): VoiceApplications
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