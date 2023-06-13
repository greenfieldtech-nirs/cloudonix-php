<?php
    /**
     * @package cloudonixPhp
     * @file    Collections/Domains.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=subscribers
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Collections;

    use Traversable;
    use ArrayIterator;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\Subscriber as EntitySubscriber;
    use Cloudonix\Entities\Apikey as EntityApikey;

    use Cloudonix\Helpers\UtilityHelper as UtilityHelper;

    /**
     * Subscribers Collection
     *
     * @property-read int           $id                            Subscriber Numeric ID
     * @property-read string        $msisdn                        Subscriber MSISDN
     * @property-read int           $domainId                      Domain Numeric ID
     * @property      bool          $active                        Subscriber Status
     * @property      string        $sipPassword                   Subscriber SIP Password
     * @property-read string        $createdAt                     Subscriber Creation Date and time
     * @property-read string        $modifiedAt                    Subscriber Last Modification Date and time
     * @property-read string        $deletedAt                     Subscriber Deletion Date and time
     * @property      EntityProfile $profile                       Subscriber Profile Object
     */
    class Subscribers extends CloudonixCollection
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        protected mixed $parent;

        public function __construct(mixed $parent)
        {
            $this->client = $parent->client;
            $this->parent = $parent;
            $this->setPath($parent->canonicalPath);
            parent::__construct();
        }

        public function list(): Subscribers
        {
            return $this;
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
            return $this->collection[$newSubscriber->msisdn];
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
                $this->canonicalPath = $branchPath . URLPATH_SUBSCRIBERS;
        }

        /**
         * Refresh the collection data from remote API
         *
         * @return $this
         */
        public function refresh(): Subscribers
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
                    $this->collection[$value->msisdn] = new EntitySubscriber($value->msisdn, $this->parent, $value);
                }
            $this->collectionCount = count($this->collection);
            return $this->collection;
        }

        public function offsetUnset(mixed $offset): void
        {
            return;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            return;
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