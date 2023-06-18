<?php
    /**
     * @package cloudonix-php
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
     * @property-read int                             $id                     Container Application Numeric ID
     * @property-read string                          $name                   Container Application Name
     * @property-read string                          $url                    Container Application URL
     * @property CollectionContainerApplicationBlocks $blocks                 Container Application Code Blocks
     */
    class ContainerApplication extends CloudonixEntity
    {
        protected mixed $client;
        protected CollectionContainerApplicationBlocks $blocks;

        /**
         * Application DataModel Object Constructor
         *
         * @param string $containerApplicationName             Cloudonix Container Application Name or ID
         * @param mixed  $parentBranch                         A reference to the previous data model node
         * @param mixed  $containerApplicationObject           A Cloudonix Container Application object
         *                                                     If $containerApplicationObject is provided, it will be
         *                                                     used to build the Container Application Entity object
         */
        public function __construct(string $containerApplicationName, mixed $parentBranch, object $containerApplicationObject = null)
        {
            $this->client = $parentBranch->client;
            parent::__construct($this, $parentBranch);
            $this->setPath($containerApplicationName, $parentBranch->canonicalPath);
            if (!is_null($containerApplicationObject)) {
                $this->buildEntityData($containerApplicationObject);
            } else {
                $this->refresh();
            }
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $branchPath): void
        {
            if (!strlen($this->canonicalPath))
                $this->canonicalPath = $branchPath . URLPATH_CONTAINER_APPLICATIONS . "/" . $string;
        }

        protected function refresh(): ContainerApplication
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        private function buildEntityData(mixed $input): void
        {
            $this->client->logger->debug(__CLASS__ . " " . __METHOD__ . " input: " . json_encode($input));
            if (!is_null($input))
                foreach ($input as $key => $value) {
                    if ($key == "blocks") {
                        $this->blocks = new CollectionContainerApplicationBlocks($this, $value);
                    } else {
                        $this->$key = $value;
                    }
                }
        }

    }