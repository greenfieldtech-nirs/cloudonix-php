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

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\VoiceApplication as EntityApplication;
    use Cloudonix\Entities\Profile as EntityProfile;

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
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     *
     * @property-read int           $id                         Voice Application Numeric ID
     * @property-read int           $domainId                   Domain Numeric ID
     * @property-read string        $name                       Voice Application Name
     * @property-read string        $type                       Voice Application Type
     * @property      string        $url                        Voice Application URL Endpoint
     * @property      string        $method                     Voice Application Endpoint HTTP Method
     * @property      bool          $active                     Voice Application Status
     * @property-read string        $createdAt                  Voice Application Creation Date and time
     * @property-read string        $modifiedAt                 Voice Application Last Modification Date and time
     * @property-read string        $deletedAt                  Voice Application Deletion Date and time
     * @property      EntityProfile $profile                    Voice Application Profile Object
     */
    class VoiceApplications extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
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
                $this->canonicalPath = $branchPath . URLPATH_APPLICATIONS;
        }

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