<?php
    /**
     * @package cloudonix-php
     * @file    Collections/CloudonixCollection.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    namespace Cloudonix\Collections;

    use ArrayIterator;
    use Cloudonix\Entities\Profile;
    use Traversable;

    /**
     * Cloudonix Collection Abstract Class
     */
    abstract class CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        protected array $collection = [];
        protected int $collectionCount = 0;

        abstract public function getPath(): string;

        abstract protected function refreshCollectionData(mixed $param): array;

        public function __construct(mixed $param)
        {
            $this->client->logger->debug(__METHOD__ . " Construction with parent " . get_class($param));
            $this->client->logger->debug(__METHOD__ . " Parent canoncialPath: " . $param->canonicalPath);
        }

        /**
         * Return the client object
         *
         * @return mixed
         */
        public function getClient(): mixed
        {
            return $this->client;
        }

        public function count(): int
        {
            if (!count($this->collection)) $this->refresh();
            return $this->collectionCount;
        }

        public function offsetExists(mixed $offset): bool
        {
            if (!count($this->collection)) $this->refresh();
            return isset($this->collection[$offset]);
        }

        public function offsetGet(mixed $offset): mixed
        {
            if (!count($this->collection)) $this->refresh();
            if (is_null($offset)) {
                return $this->collection;
            } else {
                return $this->collection[$offset];
            }
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
            if (!count($this->collection)) $this->refresh();
            return new ArrayIterator($this->collection);
        }

        public function __toString(): string
        {
            if (!count($this->collection)) $this->refresh();
            return json_encode($this->collection);
        }
    }