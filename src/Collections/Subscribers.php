<?php
    /**
     * @package cloudonix-php
     * @file    Collections/Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=subscribers
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Subscriber as EntitySubscriber;
    use Cloudonix\Entities\Domain;

    use Cloudonix\Helpers\UtilityHelper as UtilityHelper;

    /**
     * Subscribers Collection
     *
     * @see \Cloudonix\Entities\Subscriber     For more information about Subscriber Data Model
     */
    class Subscribers extends CloudonixCollection
    {
        protected mixed $client;
        protected Domain $parent;
        protected string $canonicalPath;

        public function __construct(Domain $parent)
        {
            $this->client = $parent->getClient();
            $this->parent = $parent;
            $this->setPath($parent->getPath());
            parent::__construct($this);
        }

        /**
         * Create a new API Key in the current Access Right (based upon the parent class)
         *
         * @param string      $msisdn
         * @param string|null $sipPassword      If specified, will be used as the Subscriber SIP Password.
         *                                      If ($sipPassword == "GEN") will generate a secured password.
         *
         * @return EntitySubscriber
         */
        public function newSubscriber(string $msisdn, string $sipPassword = null): EntitySubscriber
        {
            if ($sipPassword == "GEN") {
                $passwd = new UtilityHelper();
                $sipPassword = $passwd->generateSecuredPassword();
            }

            $newSubscriber = $this->client->httpConnector->request("POST", $this->getPath(), [
                'msisdn' => $msisdn,
                'sip-password' => $sipPassword
            ]);
            $this->refresh();
            return $this->getByMsisdn($msisdn);
        }

        public function getByMsisdn(string $msisdn): EntitySubscriber|bool
        {
            foreach ($this->collection as $subscriber) {
                if ($subscriber->msisdn == $msisdn) {
                    return $subscriber;
                }
            }
            return false;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $branchPath): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_SUBSCRIBERS;
        }

        public function refresh(): self
        {
            $this->refreshCollectionData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function refreshCollectionData(mixed $param): array
        {
            $this->collection = [];
            if (!is_null($param))
                foreach ($param as $value) {
                    $this->collection[] = new EntitySubscriber($value->msisdn, $this->parent, $value);
                }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        public function offsetUnset(mixed $offset): void
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . "/" . $this->collection[$offset]->msisdn);
            if ($result->code == 204) {
                parent::offsetUnset($offset);
            }
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
        }
    }