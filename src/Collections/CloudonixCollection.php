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

    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Traversable;

    abstract class CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        protected array $collection = [];

        abstract public function getPath(): string;

        abstract protected function refreshCollectionData(mixed $param): array;

        public function __construct()
        {
        }

        public function offsetExists(mixed $offset): bool
        {
            return isset($this->collection[$offset]);
        }

        public function offsetGet(mixed $offset): mixed
        {
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