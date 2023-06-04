<?php
    /**
     * @package cloudonixPhp
     * @file    Entities/Session.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=session
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;

    /**
     * Session Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Session object.
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
    class Session extends CloudonixEntity
    {
        protected mixed $client;

        /**
         * Session DataModel Object Constructor
         *
         * @param string      $token                  Session Token
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $sessionObject          A Cloudonix Session Object as stdClass
         *                                            If $sessionObject is provided, it will be used to build the
         *                                            Session Entity object
         */
        public function __construct(string $token, mixed $parentBranch, object $sessionObject = null)
        {
            $this->client = $parentBranch->client;
            parent::__construct($this->client);
            if (!is_null($sessionObject)) {
                $this->buildEntityData($sessionObject);
                $this->setPath($sessionObject->token, $sessionObject->domain);
            } else {
                $this->setPath($token, $parentBranch->domain);
                $this->refresh();
            }
        }

        /**
         * Delete an active session - will terminate the active call
         *
         * @return bool
         */
        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            if ($result->code == 204)
                return true;
            return false;
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
         * @param string $token
         * @param string $type
         *
         * @return void
         */
        protected function setPath(string $token, string $domain, string $type = "sessions"): void
        {
            $this->canonicalPath = URLPATH_CALLS . "/" . $domain;

            if ($type == "sessions")
                $this->canonicalPath .= URLPATH_SESSIONS;
            else if ($type == "incoming")
                $this->canonicalPath .= URLPATH_INCOMING;
            else if ($type == "outgoing")
                $this->canonicalPath .= URLPATH_OUTGOING;
            else if ($type == "application")
                $this->canonicalPath .= URLPATH_APPLICATION;

            if (strlen($token))
                $this->canonicalPath .= "/" . $token;
        }

        protected function refresh(): Session
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local Dnid properties
         *
         * @param mixed $dnidStdObject
         *
         * @return void
         */
        protected function buildEntityData(mixed $dnidStdObject): void
        {
            if (!is_null($dnidStdObject))

                foreach ($dnidStdObject as $key => $value) {
                    if ($key == "profile") {
                        $this->profile = new EntityProfile($value, $this);
                    } else {
                        $this->$key = $value;
                    }
                }
        }
    }