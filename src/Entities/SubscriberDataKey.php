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
        protected string $parentBranch;
        protected string $canonicalPath;

        /**
         * VoiceApplication Subscriber Data DataModel Object Constructor
         *
         * @param mixed      $voiceApplicationObject        Cloudonix Voice Application Object
         * @param string     $subscriber                    Cloudonix Subscriber ID or MSISDN
         * @param mixed|null $voiceApplicationDataKeyObject A Cloudonix Voice Application Data Key Object
         */
        public function __construct(mixed $voiceApplicationObject, string $subscriber, mixed $voiceApplicationDataKeyObject = null)
        {
            $this->client = $voiceApplicationObject->client;
            parent::__construct($this, $voiceApplicationObject);

            if (!is_null($voiceApplicationDataKeyObject)) {
                $this->buildEntityData($voiceApplicationDataKeyObject);
            }
            $this->setPath($voiceApplicationObject->canonicalPath, $subscriber);
        }

        /**
         * Delete a voice application subsriber data key
         *
         * @return bool
         */
        public function delete(): bool
        {
            $result = $this->buildEntityData($this->client->httpConnector->request("DELETE", $this->getPath()));
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " result: " . json_encode($result));
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

        private function buildEntityData(mixed $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            if (!is_null($input))
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