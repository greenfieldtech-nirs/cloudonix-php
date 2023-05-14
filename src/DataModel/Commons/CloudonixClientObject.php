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
     * @file    CloudonixObject.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @created 2023-05-09
     */

    namespace Cloudonix\DataModel\Commons;

    use Cloudonix\Client as CloudonixClient;

    /**
     * CloudonixObject is an abstract class serving as the basis of all other
     * Cloudonix data models.
     *
     * @package cloudonixPhp
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     */
    abstract class CloudonixClientObject
    {
        /**
         * @var CloudonixClient An instance of the CloudonixClient object
         */
        public CloudonixClient $client;

        public function __construct(CloudonixClient $client)
        {
            $this->client = $client;
        }

        abstract public function getPath(): string;

        public function __toString(): string
        {
            return json_encode($this);
        }

        public function __set(string $name, mixed $value)
        {
            $this->$name = $value;
        }

        public function __get(string $name): mixed
        {
            return $this->$name;
        }

    }