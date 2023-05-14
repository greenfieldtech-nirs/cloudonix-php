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
     * @filename: CloudonixCollection.php
     * @author  :   nirs
     * @created :  2023-05-11
     */

    namespace Cloudonix\DataModel\Commons;

    use ArrayIterator;
    use Traversable;

    class CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        private array $collection;

        public function __construct(array $collection = [])
        {
            $this->collection = $collection;
        }

        public function offsetExists(mixed $offset): bool
        {
            return isset($this->collection[$offset]);
        }

        public function offsetGet(mixed $offset): mixed
        {
            if (get_called_class() != "Cloudonix\DataModel\Domains") {
                if (is_numeric($offset))
                    return $this->collection[$offset];

                list($searchKey, $searchValue) = explode(":", $offset);

                foreach ($this->collection as $domainObject) {
                    if ((strtoupper($searchKey) == "ID") && ($domainObject->id == $searchValue)) {
                        return ($domainObject);
                    } else if ((strtoupper($searchKey) == "UUID") && ($domainObject->uuid == $searchValue)) {
                        return ($domainObject);
                    } else if ((strtoupper($searchKey) == "DOMAIN") && ($domainObject->domain == $searchValue)) {
                        return ($domainObject);
                    }
                }
            }

            return $this->collection[$offset];
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if (is_null($offset)) {
                $this->collection[] = $value;
            } else {
                $this->collection[$offset] = $value;
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            unset($this->collection[$offset]);
        }

        /**
         * @inheritDoc
         */
        public function getIterator(): Traversable
        {
            return new ArrayIterator($this->collection);
        }

        public function __toString(): string
        {
            return json_encode($this->collection);
        }
    }