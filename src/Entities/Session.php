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
            }
        }

        /**
         * Update the current timeLimit of an active session
         *
         * @param int $timelimit
         *
         * @return $this
         */
        public function updateTimeLimit(int $timelimit): Session
        {
            $updatedSession = $this->client->httpConnector->request("PATCH", $this->getPath(), ['timeLimit' => $timelimit]);
            $this->buildEntityData($updatedSession);
            return $this;
        }

        /**
         * Notify that a remote subscriber device (either a mobile application or web application) is currently ringing
         *
         * The provided $msisdn represents an existing Cloudonix subscriber, that has the $token associated with it.
         * This method is used in RegFree Dialing implementations.
         *
         * @param string $msisdn
         * @param string $token
         *
         * @return $this
         */
        public function notifyRinging(string $msisdn, string $token): Session
        {
            $result = $this->client->httpConnector->request("GET", URLPATH_CALLS . "/" . $this->domain . "/ringing/" . $msisdn . "/" . $token);
            if ($result->code == 200)
                $this->refresh();
            return $this;
        }

        /**
         * Transfer session control to a new voice application, provided as CXML.
         *
         * @param string $cxml
         *
         * @return $this
         */
        public function switchVoiceApplicationToCXML(string $cxml): Session
        {
            $this->buildEntityData($this->client->httpConnector->request("POST", $this->getPath() . "/application", ['cxml' => $cxml]));
            return $this;
        }

        /**
         * Transfer session control to a new voice application, provided a remote URL.
         *
         * @param string $url
         * @param string $method
         *
         * @return $this
         */
        public function switchVoiceApplicationToUrl(string $url, string $method = "POST"): Session
        {
            $this->buildEntityData($this->client->httpConnector->request("POST", $this->getPath() . "/application", ['url' => $url, 'method' => $method]));
            return $this;
        }

        /**
         * Fork an active session/call, transferring each participant to a new voice application.
         *
         * The $caller and $callee are represented by an array, which may consist of the following
         * keys:
         *      - cxml: An embedded CXML script to be executed.
         *      - url and method: A remotely provided voice application resource
         *
         * Example:
         *
         * <code>
         *
         * $caller = [
         * "cxml" => "<Response><Dial><Conference>room 101</Conference></Dial><Hangup/></Response>"
         * ];
         *
         * $callee = [
         * "url" => "https://some.remote.server/some.script",
         * "method" => "POST"
         * ];
         *
         * </code>
         *
         * For more information about this functionality, please refer to:
         * https://dev.docs.cloudonix.io/#/platform/api-core/sessions?id=session-control-fork-routed-call
         *
         * @see https://dev.docs.cloudonix.io/#/platform/api-core/sessions?id=session-control-fork-routed-call
         *
         * @param array  $caller        A new Voice Application resource description array, as above
         * @param array  $callee        A new Voice Application resource description array, as above
         * @param string $callback      Optional remote session update callback URL
         *
         * @return array
         */
        public function forkRoutedCall(array $caller, array $callee, string $callback = ""): array
        {
            $result = [];

            $requestData = [
                "caller" => $caller,
                "callee" => $callee
            ];
            if (strlen($callback))
                $requestData["callback"] = $callback;

            $forkResult = $this->client->httpConnector->request("POST", $this->getPath() . "/fork", $requestData);
            $result['caller'] = $forkResult->caller;
            $result['callee'] = $forkResult->callee;

            return $result;
        }

        /**
         * Delete an active session - will terminate the active call
         *
         * When terminating a session, $reason may be provided as one of the following:
         *      - timeout    The subscriber can't be reached
         *      - denied     The subscriber actively rejected the call
         *      - busy       The subscriber is currently busy on another call
         *      - nocredit   The subscriber has run out of credit (useful for billing related use-cases)
         *
         * @param string $reason
         *
         * @return bool
         */
        public function delete(string $reason = ""): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath() . (strlen($reason) ? "?reason=" . $reason : ""));
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
         *
         * @return void
         */
        protected function setPath(string $token = "", string $domain): void
        {
            $this->canonicalPath = URLPATH_CALLS . "/" . $domain . URLPATH_SESSIONS;
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
        private function buildEntityData(mixed $dnidStdObject): void
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