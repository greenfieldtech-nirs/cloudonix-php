<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Dnids.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=dnids
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Session as EntitySession;
    use Cloudonix\Entities\Domain;


    /**
     * Sessions Collection
     */
    class Sessions extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        protected mixed $client;
        protected Domain $parent;
        protected string $canonicalPath;

        public function __construct(Domain $parent)
        {
            $this->client = $parent->getClient();
            $this->setPath($parent->domain);
            $this->parent = $parent;
            parent::__construct($this);
        }

        public function startOutboundCall(string $destination, string $callerId = "", int $timeout = 60, array $appData = ['application' => 'call-routing']): EntitySession
        {
            $appData['destination'] = $destination;
            $appData['timeout'] = $timeout;
            $appData['caller-id'] = $callerId;

            $newSessionResult = $this->client->httpConnector->request("POST", URLPATH_CALLS . "/" . $this->parent->domain . "/application", $appData);
            return new EntitySession($newSessionResult->token, $this, $newSessionResult);
        }

        public function startSubscriberSession(string $subscriber, string $destination, string $callback, int $timeLimit = 1800): EntitySession
        {
            $newSessionResult = $this->client->httpConnector->request("POST", URLPATH_CALLS . "/" . $this->parent->domain . "/outgoing/" . $subscriber, [
                'timeLimit' => $timeLimit,
                'callback' => $callback,
                'destination' => $destination
            ]);
            return new EntitySession($newSessionResult->token, $this, $newSessionResult);
        }

        public function incoming(int $limit = 1000): Sessions
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath() . FILTER_INCOMING . "&limit=" . $limit));
            return $this;
        }

        public function outgoing(int $limit = 1000): self
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath() . FILTER_OUTGOING . "&limit=" . $limit));
            return $this;
        }

        public function application(int $limit = 1000): self
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath() . FILTER_APPLICATION . "&limit=" . $limit));
            return $this;
        }

        public function terminateByToken(string $token): self
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $token);
            if ($result->code == 204) {
                $this->refresh();
            }
            return $this;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $domain): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = URLPATH_CALLS . "/" . $domain . URLPATH_SESSIONS;
        }

        public function refresh(): self
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function refreshCollectionData(object|array $param): array
        {
            $this->collection = [];
            foreach ($param as $key => $value) {
                if (is_object($value)) {
                    $this->collection[] = new EntitySession($value->token, $this->parent, $value);
                }
            }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }

        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $this->collection[$offset]->token);
            if ($result->code == 204) {
                parent::offsetUnset($offset);
            }
        }
    }