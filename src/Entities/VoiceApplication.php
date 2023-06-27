<?php
    /**
     * @package cloudonix-php
     * @filename: Entities/VoiceApplication.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Collections\Dnids as CollectionDnids;
    use Cloudonix\Collections\SubscriberDataKeys as CollectionSubscriberData;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;
    use Cloudonix\Entities\Dnid as EntityDnid;

    use Cloudonix\Helpers\UtilityHelper as UtilityHelper;

    /**
     * Voice Application Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Voice Application object.
     *
     * @property-read int           $id                     Application Numeric ID
     * @property-read int           $domainId               Domain Numeric ID
     * @property-read string        $name                   Application Name
     * @property      bool          $active                 Application Status
     * @property-read string        $createdAt              Application Creation Date and time
     * @property-read string        $modifiedAt             Application Last Modification Date and time
     * @property-read string        $deletedAt              Application Deletion Date and time
     * @property-read string        $type                   Application type
     * @property      string        $url                    Application URL
     * @property      string        $method                 Application Method (GET or POST)
     * @property      EntityProfile $profile                Application Profile Object
     */
    class VoiceApplication extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath;
        public CollectionDnids $collectionDnids;

        /**
         * Application DataModel Object Constructor
         *
         * @param string          $voiceApplicationName        Cloudonix Voice Application Name or ID
         * @param CloudonixEntity $parent                      The parent object that created this object
         * @param ?object         $inputObject                 A Cloudonix Application object
         *                                                     If $voiceApplicationObject is provided, it will be used
         *                                                     to build the Application Entity object
         */
        public function __construct(string $voiceApplicationName, CloudonixEntity $parent, ?object $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($this, $parent);

            if (!is_null($inputObject)) {
                $this->setPath($inputObject->name, $parent->getPath());
                $this->buildEntityData($inputObject);
            } else {
                $this->setPath($voiceApplicationName, $parent->getPath());
            }
        }

        /**
         * Return a DNID object, assigned to the voice application
         *
         * @param string $dnid
         *
         * @return Dnid
         */
        public function dnid(string $dnid): EntityDnid
        {
            return new Dnid($dnid, $this);
        }

        /**
         * Return a collection of DNID objects, assigned to the voice application
         *
         * @return CollectionDnids
         */
        public function dnids(): CollectionDnids
        {
            if (!isset($this->collectionDnids))
                $this->collectionDnids = new CollectionDnids($this);

            return $this->collectionDnids;
        }

        /**
         * Return a voice application subscriber data object
         *
         * @param string $subscriberMsisdn
         *
         * @return CollectionSubscriberData
         */
        public function subscriberData(string $subscriberMsisdn): CollectionSubscriberData
        {
            return new CollectionSubscriberData($this, $subscriberMsisdn);
        }

        /**
         * Assign a new DNID number to a voice application, based upon a DNID prefix
         *
         * @param string $dnid
         *
         * @return Dnid
         */
        public function newDnidPrefix(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => false,
                'prefix' => true,
                'asteriskCompatible' => false
            ]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($dnidResult));
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        /**
         * Assign a new DNID number to a voice application, based upon a regular expression
         *
         * @param string $dnid
         *
         * @return Dnid
         */
        public function newDnidRegex(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => true,
                'prefix' => false,
                'asteriskCompatible' => false
            ]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($dnidResult));
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        /**
         * Assign a new DNID number to a voice application, based upon an Asterisk dialplan expression
         *
         * @param string $dnid
         *
         * @return Dnid
         */
        public function newDnidAsterisk(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => false,
                'prefix' => false,
                'asteriskCompatible' => true
            ]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($dnidResult));
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        /**
         * Assign a new DNID number to a voice application, based upon a DNID pattern match
         *
         * @param string $dnid
         *
         * @return Dnid
         */
        public function newDnidPattern(string $dnid): EntityDnid
        {
            $dnidResult = $this->client->httpConnector->request("POST", $this->getPath() . "/dnids", [
                'source' => $dnid,
                'expression' => false,
                'prefix' => false,
                'asteriskCompatible' => false
            ]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($dnidResult));
            return new EntityDnid($dnidResult->id, $this, $dnidResult);
        }

        /**
         * Set the Voice Application remote URL and method
         *
         * @param string $url    Remote Voice Application URL
         * @param string $method Remote Voice Application URL method [GET/POST]
         *
         * @return $this            A refreshed Voice Application object
         */
        public function setApplicationUrl(string $url, string $method = "POST"): VoiceApplication
        {
            if ((strtoupper($method) != "POST") && (strtoupper($method) != "GET"))
                return $this;

            $utilityHelper = new UtilityHelper();
            $validatedUrl = $utilityHelper->cleanUrl($url);

            if (!$validatedUrl)
                return $this;

            $patchResult = $this->client->httpConnector->request("PATCH", $this->getPath(), [
                'url' => $validatedUrl,
                'method' => strtoupper($method)
            ]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($patchResult));

            return $this->refresh();
        }

        /**
         * Enable/Disable a DNID number
         *
         * @param bool $status
         *
         * @return Domain
         */
        public function setActive(bool $status): Domain
        {
            $patchResult = $this->client->httpConnector->request("PATCH", $this->getPath(), ['active' => $status]);
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($patchResult));
            return $this->refresh();
        }

        /**
         * Delete a DNID
         *
         * @return bool
         */
        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));

            if ($result->code == 204)
                return true;
            return false;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $branchPath): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_APPLICATIONS . "/" . $string;
        }

        protected function refresh(): VoiceApplication
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function buildEntityData(object|array $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            foreach ($input as $key => $value) {
                if ($key == "profile") {
                    $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " creating profile with this: " . json_encode($this));
                    $this->profile = new EntityProfile($this, $value);
                } else if ($key == "domain") {
                    continue;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }