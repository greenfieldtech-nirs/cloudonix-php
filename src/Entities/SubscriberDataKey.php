<?php
    /**
     * @package cloudonix-php
     * @file    Entities/SubscriberDataKey.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\VoiceApplication as EntityVoiceApplication;

    /**
     * Subscriber Data Model Entity
     *
     * Cloudonix voice applications allow the developer to securely store subscriber related information, in a
     * similar fashion to how web applications can store data in cookies. Data is stored in string format, providing
     * the developer with maximum flexibility in storing JSON or other encoded data.
     *
     * @property      string $key                    Subscriber data key
     * @property      string $data                   Subscriber data storage
     * @property-read string $createdAt              Subscriber data Creation Date and time
     * @property-read string $modifiedAt             Subscriber data Last Modification Date and time
     * @property-read string $deletedAt              Subscriber data Last Deletion Date and time
     */
    class SubscriberDataKey extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath;

        /**
         * VoiceApplication Subscriber Data DataModel Object Constructor
         *
         * @param VoiceApplication $parent      Cloudonix Voice Application Object
         * @param string           $subscriber  Cloudonix Subscriber ID or MSISDN
         * @param ?object          $inputObject A Cloudonix Voice Application Data Key Object
         */
        public function __construct(VoiceApplication $parent, string $subscriber, ?object $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($this, $parent);

            if (!is_null($inputObject)) {
                $this->buildEntityData($inputObject);
            }
            $this->setPath($parent->getPath(), $subscriber);
        }

        /**
         * Delete a voice application subscriber data key
         *
         * @return bool
         */
        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
            $this->buildEntityData($result);
            if ($result->code == 204)
                return true;
            return false;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $subscriber): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $string . "/subscribers/" . $subscriber;
        }

        public function refresh(): SubscriberDataKey
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        public function __get(mixed $name): SubscriberDataKey
        {
            return $this;
        }

        protected function buildEntityData(object|array $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            foreach ($input as $key => $value) {
                if ($key == "application")
                    continue;
                else if ($key == "subscriber")
                    continue;
                else
                    $this->$key = $value;
            }
        }
    }