<?php
    /**
     * @package cloudonix-php
     * @filename: Entities/VoiceApplication.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=applications
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created :  2023-05-14
     */

    namespace Cloudonix\Entities;

    use Cloudonix\Collections\CloudonixCollection;
    use Cloudonix\Entities\CloudonixEntity as CloudonixEntity;
    use Cloudonix\Entities\CodeBlock as EntityCodeBlock;

    /**
     * Hosted Application Data Model Entity
     *
     * This class represents the generalised form of a Cloudonix Hosted Application object.
     *
     * @property-read int    $id                     Hosted Application Numeric ID
     * @property-read string $name                   Hosted Application Name
     * @property      string $url                    Hosted Application URL
     * @property      array  $blocks                 Hosted Application Code Blocks
     */
    class HostedApplication extends CloudonixEntity
    {
        protected mixed $client;
        protected string $canonicalPath;

        public array $blocks = [];

        /**
         * Application DataModel Object Constructor
         *
         * @param string                              $hostedApplicationName Cloudonix Hosted Application Name or ID
         * @param CloudonixEntity|CloudonixCollection $parent                The parent object that created this object
         * @param ?object                             $inputObject           A Cloudonix Hosted-Application object
         *                                                                   If $inputObject is provided, it will be
         *                                                                   used to build the Hosted Application
         *                                                                   Entity object
         */
        public function __construct(string $hostedApplicationName, CloudonixEntity|CloudonixCollection $parent, ?object $inputObject = null)
        {
            $this->client = $parent->getClient();
            parent::__construct($this, $parent);

            $this->setPath($hostedApplicationName, $parent->getPath());
            if (!is_null($inputObject)) {
                $this->buildEntityData($inputObject);
            } else {
                $this->refresh();
            }
        }

        public function getBlockByName(string $name): CodeBlock
        {
            foreach ($this->blocks as $block) {
                if ($block->name == $name) {
                    return $block;
                }
            }
        }

        public function delete(): bool
        {
            $result = $this->client->httpConnector->request("DELETE", $this->getPath());
            if ($result->code == 204)
                return true;
            return false;
        }

        public function getPath(): string
        {
            return $this->canonicalPath;
        }

        protected function setPath(string $string, string $parentPath): void
        {
            if (!isset($this->canonicalPath))
                $this->canonicalPath = $parentPath . URLPATH_CONTAINER_APPLICATIONS . "/" . $string;
        }

        protected function refresh(): HostedApplication
        {
            $this->buildEntityData($this->client->httpConnector->request("GET", $this->getPath()));
            return $this;
        }

        protected function buildEntityData(object|array $input): void
        {
            foreach ($input as $key => $value) {
                if ($key == "blocks") {
                    foreach ($value as $block) {
                        $this->blocks[] = new EntityCodeBlock($block->name, $this, $block);
                    }
                } else {
                    $this->$key = $value;
                }
            }
        }
    }