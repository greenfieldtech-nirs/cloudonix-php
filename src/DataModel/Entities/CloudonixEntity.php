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
     * @file    Entities/CloudonixEntity.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @created 2023-05-14
     */

    namespace Cloudonix\DataModel\Entities;

    /**
     * Cloudonix Entity Class
     * This class represents the generalised form of a Cloudonix data model entity.
     *
     * @package cloudonixPhp
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     *
     * @property-read int    $code             Cloudonix REST API Result Code
     * @property-read string $message          Cloudonix REST API Result Message
     * @property-read string $restResponse     Cloudonix REST API Response
     */
    abstract class CloudonixEntity
    {
        public function __construct(mixed $stdObject)
        {
            foreach ($stdObject as $key => $value) {
                if (!is_object($value)) $this->$key = $value;
            }
        }

        abstract public function getPath(): string;

        public function __toString(): string
        {
            return json_encode($this);
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