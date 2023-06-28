<?php
    /**
     * @package cloudonix-php
     * @filename: Entities/VoiceApplication.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=hosted-applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;
    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;

    /**
     * Hosted Application Code Block Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Hosted Application Code Block object.
     *
     * @property-read int    $id                       Code Block Numeric ID
     * @property-read string $name                     Code Block Name
     * @property-read string $runtime                  Code Block Runtime
     * @property      string $code                     Code Block Source Code
     */
    class CodeBlock extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath;

        /**
         * Code Block DataModel Object Constructor
         *
         * @param string                              $codeBlockName Code Block Name
         * @param CloudonixEntity|CloudonixCollection $parent        The parent object that created this object
         * @param ?object                             $inputObject   A Cloudonix Hosted-Application object
         *                                                           If $inputObject is provided, it will be used
         *                                                           to build the Hosted Application Entity object
         */
        public function __construct(string $codeBlockName, CloudonixEntity|CloudonixCollection $parent, ?object $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($this, $parent);

            if (!is_null($inputObject)) {
                $this->setPath($inputObject->name, $parent->getPath());
                $this->buildEntityData($inputObject);
            } else {
                $this->setPath($codeBlockName, $parent->getPath());
            }
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $parentPath): void
        {
            if (!isset($this->canonicalPath)){
                $this->canonicalPath = $parentPath . "/blocks/" . $string;
            }
        }

        protected function refresh(): CodeBlock
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        public function __get(mixed $name)
        {
            return $this->$name;
        }

        public function __set(string $name, mixed $value)
        {
            $this->$name = $value;
        }
    }