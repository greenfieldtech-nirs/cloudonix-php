<?php
    /**
     * @package cloudonix-php
     * @file    Collections/ContainerApplicationBlocks.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=hosted-applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Entities\ContainerApplicationBlock as EntityContainerApplicationBlock;
    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;

    /**
     * Container Application Blocks Collection
     *
     * @property-read   int    $id                          Container Application Block Numeric ID
     * @property-read   string $name                        Container Application Block Name
     * @property-read   string $runtime                     Container Application Block Runtime
     * @property        string $code                        Container Application Code
     */
    class ContainerApplicationBlocks extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        public mixed $client;
        public string $canonicalPath = "";
        private mixed $parent;

        public function __construct(mixed $parent, mixed $applicationBlocks)
        {
            $this->client = $parent->client;
            parent::__construct($this);
            $this->parent = $parent;
            $this->canonicalPath = $parent->canonicalPath;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function refresh(): ContainerApplicationBlocks
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function refreshCollectionData(mixed $param): array
        {
            $this->collection = [];
            if (!is_null($param) && !isset($param->code))
                foreach ($param->blocks as $key => $value) {
                    $this->collection[$value->name] = new EntityContainerApplicationBlock($value->name, $this->parent, $value);
                }
            return $this->collection;
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
