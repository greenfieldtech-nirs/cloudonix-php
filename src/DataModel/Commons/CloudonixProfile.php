<?php
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
     * @project cloudonix-php
     * @file    CloudonixProfile.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @created 2023-05-09
     */

    namespace Cloudonix\DataModel\Commons;

    use Cloudonix\Client as CloudonixClient;
    use Exception;
    use stdClass;

    /**
     * CloudonixProfile is a generic representation of the Cloudonix Profile data model.
     * Various data models include the ability to assign a profile to them. This class
     * provides the interface to these.
     *
     * @package cloudonixPhp
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     */
    class CloudonixProfile
    {
        private CloudonixClient $client;
        private string $basePath;
        protected array $arrayProfile;
        protected stdClass $objectProfile;
        private stdClass $profile;

        public function __construct(CloudonixClient $client, string $basePath = null, stdClass $profile)
        {
            $this->client = $client;
            $this->basePath = $basePath;
            $this->setProfileProperties($profile);
            $this->profile = $profile;
        }

        public function __toString(): string
        {
            return json_encode($this->arrayProfile);
        }

        public function __get(string $name): mixed
        {
            return $this->objectProfile->$name;
        }

        private function setProfileProperties(stdClass $profile): void
        {
            $objectVariables = get_object_vars($profile);
            foreach ($objectVariables as $key => $value) {
                $this->arrayProfile[$key] = $value;
            }
            $this->objectProfile = (object)$this->arrayProfile;
        }

    }