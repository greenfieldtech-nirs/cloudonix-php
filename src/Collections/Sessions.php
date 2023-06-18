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

    use ArrayIterator;
    use Traversable;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Session as EntitySession;

    /**
     * Sessions Collection
     *
     * @property-read int           $id                                      Session Numeric ID
     * @property-read int           $domainId                                Session Domain Numeric ID
     * @property-read string        $domain                                  Domain Name
     * @property-read int           $subscriberId                            Domain Subscriber Numeric ID
     * @property-read string        $destination                             The destination MSISDN
     * @property-read string        $callerId                                The caller ID MSISDN
     * @property-read string        $direction                               The direction of the call (see
     *                https://dev.docs.cloudonix.io/#/platform/api-core/models?id=session for additional information)
     * @property-read string        $token                                   Session unique token identifier
     * @property-read int           $timeLimit                               Session time limit, if defined, in seconds
     * @property-read EntityProfile $profile                                 Session profile (see
     *                https://dev.docs.cloudonix.io/#/platform/api-core/models?id=session for additional information)
     * @property-read int           $callStartTime                           Session start time, in UNIX EPOCH
     *                milliseconds
     * @property-read int           $callAnswerTime                          Session answer time, in UNIX EPOCH
     *                milliseconds
     * @property-read int           $callEndTime                             Session end time, in UNIX EPOCH
     *                milliseconds
     * @property-read string        $callback                                Session update URL as defined in the
     *                domain
     * @property-read object        $routes                                  Session LCR Routing plan, if defined via
     *                LCR API
     * @property-read string        $status                                  Session current status (see
     *                https://dev.docs.cloudonix.io/#/platform/api-core/models?id=session for additional information)
     *
     * @property-read string        $error                                   Session last encountered error
     */
    class Sessions extends CloudonixCollection implements \IteratorAggregate, \ArrayAccess
    {
        public mixed $client;
        private mixed $parent;

        public function __construct(mixed $parent)
        {
            $this->client = $parent->client;
            parent::__construct($this);
            $this->parent = $parent;
            $this->setPath($parent->domain);
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

        public function outgoing(int $limit = 1000): Sessions
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath() . FILTER_OUTGOING . "&limit=" . $limit));
            return $this;
        }

        public function application(int $limit = 1000): Sessions
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath() . FILTER_APPLICATION . "&limit=" . $limit));
            return $this;
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
         * @param string $domain
         *
         * @return void
         */
        protected function setPath(string $domain): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = URLPATH_CALLS . "/" . $domain . URLPATH_SESSIONS;
        }

        /**
         * Refresh the collection
         *
         * @return $this
         */
        public function refresh(): Sessions
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
                    if (is_object($value)) {
                        $this->collection[$value->token] = new EntitySession($value->token, $this->parent, $value);
                    }
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