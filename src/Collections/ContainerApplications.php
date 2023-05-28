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
    use Cloudonix\Entities\ContainerApplication;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\VoiceApplication as EntityApplication;
    use Cloudonix\Entities\ContainerApplication as EntityContainerApplication;

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
     * Domains Collection
     *
     * @package cloudonixPhp
     * @file    Collections/Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=domains
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    class ContainerApplications extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
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
         * @param string $string
         *
         * @return void
         */
        protected function setPath(string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_CONTAINER_APPLICATIONS;
        }

        public function refresh(): ContainerApplications
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        private function getContainerApplication(mixed $name): EntityContainerApplication
        {
            return new EntityContainerApplication($name, $this->parent);
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
            if (!is_null($param) && !isset($param->code))
                foreach ($param as $key => $value) {
                    $this->collection[$value->name] = new EntityContainerApplication($value->name, $this->parent, $value);
                }
            return $this->collection;
        }

        public function offsetGet(mixed $offset): mixed
        {
            $this->getContainerApplication($offset);
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