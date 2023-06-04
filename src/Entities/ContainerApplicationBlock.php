<?php
    /**
     * @package cloudonixPhp
     * @filename: Entities/ContainerApplicationBlock.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=hosted-applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;

    /**
     * Container Application Block Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Container Application Block object.
     *
     * @property-read int    $id                        Container Application Block Numeric ID
     * @property-read string $name                      Container Application Block Name
     * @property-read string $runtime                   Container Application Block Runtime
     * @property      string $code                      Container Application Block Source Code
     */
    class ContainerApplicationBlock extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";

        /**
         * Application DataModel Object Constructor
         *
         * @param string      $containerApplicationBlockName   Cloudonix Container Application Block Name or ID
         * @param mixed       $parentBranch                    A reference to the previous data model node
         * @param object|null $containerApplicationBlockObject Cloudonix Container Application Block object
         *                                                     If $containerApplicationBlockObject is provided, it will be used
         *                                                     to build the Application Entity object
         */
        public function __construct(string $containerApplicationBlockName, mixed $parentBranch, object $containerApplicationBlockObject = null)
        {
            parent::__construct($this);
            $this->client = $parentBranch->client;
            $this->setPath($containerApplicationBlockName, $parentBranch->canonicalPath);
            if (is_null($containerApplicationBlockObject)) {
                $this->refresh();
            } else {
                $this->buildEntityData($containerApplicationBlockObject);
            }
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
         *
         * @return void
         */
        protected function setPath(string $string, string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath;
        }

        protected function refresh(): ContainerApplicationBlock
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local Container Application Block properties
         *
         * @param mixed $applicationStdObject
         *
         * @return void
         */
        protected function buildEntityData(mixed $applicationStdObject): void
        {
            if (!is_null($applicationStdObject))
                foreach ($applicationStdObject as $key => $value) {
                    $this->$key = $value;
                }
        }
    }