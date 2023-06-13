<?php
    /**
     * @package cloudonixPhp
     * @filename: Entities/ContainerApplication.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=hosted-applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     */
    namespace Cloudonix\Entities;

    use Cloudonix\Collections\ContainerApplicationBlocks as CollectionContainerApplicationBlocks;

    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\ContainerApplicationBlock as EntityContainerApplicationBlock;

    /**
     * Container Application Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Application object.
     *
     * @property-read int                                  $id                     Container Application Numeric ID
     * @property-read string                               $name                   Container Application Name
     * @property-read string                               $url                    Container Application URL
     * @property-read CollectionContainerApplicationBlocks $blocks                 Container Application Code Blocks
     */
    class ContainerApplication extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath = "";
        protected CollectionContainerApplicationBlocks $blocks;

        /**
         * Application DataModel Object Constructor
         *
         * @param string      $voiceApplicationName            Cloudonix Container Application Name or ID
         * @param mixed       $parentBranch                    A reference to the previous data model node
         * @param object|null $voiceApplicationObject          A Cloudonix Container Application object
         *                                                     If $containerApplicationObject is provided, it will be
         *                                                     used to build the Container Application Entity object
         */
        public function __construct(string $voiceApplicationName, mixed $parentBranch, object $voiceApplicationObject = null)
        {
            $this->client = $parentBranch->client;
            $this->setPath($voiceApplicationName, $parentBranch->canonicalPath);
            if (is_null($voiceApplicationObject)) {
                $this->refresh();
            } else {
                $this->buildEntityData($voiceApplicationObject);
            }
            parent::__construct($this);

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
        protected function setPath(string $string, string $branchPath)
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_CONTAINER_APPLICATIONS . "/" . $string;
        }

        protected function refresh(): ContainerApplication
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        /**
         * Build the local Application properties
         *
         * @param mixed $applicationStdObject
         *
         * @return void
         */
        private function buildEntityData(mixed $applicationStdObject): void
        {
            if (!is_null($applicationStdObject))
                foreach ($applicationStdObject as $key => $value) {
                    if ($key == "blocks") {
                        $this->blocks = new CollectionContainerApplicationBlocks($this, $value);
                    } else {
                        $this->$key = $value;
                    }
                }
        }


    }