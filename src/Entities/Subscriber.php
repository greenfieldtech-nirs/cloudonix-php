<?php
    /**
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     *
     * @project :  cloudonix-php
     * @filename: Domain.php
     * @author  :   nirs
     * @created :  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Helpers\PasswordHelper as PasswordHelper;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\Profile as EntityProfile;

    /**
     * <code>
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     * </code>
     *
     * Subscriber Data Model Entity
     * This class represents the generalised form of a Cloudonix Subscriber object.
     * Profiles are used in Cloudonix with various data models - and is free formed.
     *
     * @package cloudonixPhp
     * @file    Entities/Subscriber.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=subscriber
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     *
     * @property-read int    $id                            Subscriber Numeric ID
     * @property-read string $msisdn                        Subscriber MSISDN
     * @property-read int    $domainId                      Domain Numeric ID
     * @property      bool   $active                        Subscriber Status
     * @property      string $sipPassword                   Subscriber SIP Password
     * @property-read string $createdAt                     Subscriber Creation Date and time
     * @property-read string $modifiedAt                    Subscriber Last Modification Date and time
     * @property-read string $deletedAt                     Subscriber Deletion Date and time
     * @property      EntityProfile $profile                Subscriber Profile Object
     */
    class Subscriber extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";

        /**
         * Subscriber DataModel Object Constructor
         *
         * @param string      $subscriber             A Cloudonix Subscriber Name (in cockpit.cloudonix.io also known
         *                                            as MSISDN)
         * @param mixed       $parentBranch           A reference to the previous data model node
         * @param object|null $subscriberObject       A Cloudonix Subscriber Object as stdClass
         *                                            If $subscriberObject is provided, it will be used to build the
         *                                            Domain Entity object
         */
        public function __construct(string $subscriber, mixed $parentBranch, object $subscriberObject = null)
        {
            $this->client = $parentBranch->client;
            $this->setPath($subscriber, $parentBranch->canonicalPath);
            $this->buildEntityData($subscriberObject);
            parent::__construct($subscriberObject);
        }

        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            if ($result->code == 204)
                return true;
            return false;
        }

        public function resetSipPassword(string $password = null): Subscriber
        {
            if ($password == "GEN") {
                $passwd = new PasswordHelper();
                $password = $passwd->generateSecuredPassword();
            }
            $result = $this->client->httpConnector->request("PATCH", $this->getPath(), [ 'sip-password' => $password]);
            $this->buildEntityData($result);
            return $this;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_SUBSCRIBERS . "/" . $string;
        }

        protected function buildEntityData(mixed $input)
        {
            foreach ($input as $key => $value) {
                if ($key == "profile") {
                    $this->profile = new EntityProfile($value, $this);
                } else {
                    $this->$key = $value;
                }
            }
        }

        protected function refresh(): Subscriber
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        public function __toString(): string
        {
            return json_encode($this->refresh());
        }
    }