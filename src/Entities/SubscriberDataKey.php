<?php
    /**
     * @package cloudonixPhp
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

        /**
         * VoiceApplication Subscriber Data DataModel Object Constructor
         *
         * @param EntityVoiceApplication $voiceApplicationObject Cloudonix Voice Application Object
         * @param string                 $subscriber             Cloudonix Subscriber ID or MSISDN
         */
        public function __construct(EntityVoiceApplication $voiceApplicationObject, string $subscriber, mixed $subscriberDataObject)
        {
            $this->client = $voiceApplicationObject->client;
            parent::__construct($this);
            if (!is_null($subscriberDataObject)) {
                $this->buildEntityData($subscriberDataObject);
                $this->setPath($subscriberDataObject->canonicalPath, $subscriber);
            } else {
                $this->setPath($voiceApplicationObject->canonicalPath, $subscriber);
            }
        }

        public function delete(): bool
        {
            $result = $this->buildEntityData($this->client->httpConnector->request("DELETE", $this->getPath()));
            if ($result->code == 204)
                return true;
            return false;
        }

        /**
         * Return the entity REST API canonical path
         *
         * @return string
         */
        public function getPath()
        {
            return $this->canonicalPath;
        }

        /**
         * Set the entity REST API canonical path
         *
         * @param string $string
         * @param string $subscriber
         *
         * @return void
         */
        protected function setPath(string $string, string $subscriber): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $string . "/subscriber-data/" . $subscriber;
        }

        /**
         * Refresh the local storage with the remote data
         *
         * @return $this
         */
        public function refresh(): SubscriberDataKey
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the voice application subscribers data
         *
         * @param mixed $inputObject
         *
         * @return void
         */
        private function buildEntityData(mixed $inputObject): void
        {
            if (!is_null($inputObject))
                foreach ($inputObject as $key => $value) {
                    if ($key == "application")
                        continue;
                    else if ($key == "subscriber")
                        continue;
                    else
                        $this->$key = $value;
                }
        }
    }