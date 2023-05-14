<?php
    namespace Cloudonix\DataModel\Entities;

    use Cloudonix\DataModel\Entities\CloudonixEntity as CloudonixEntity;

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
     * Profile Data Model Entity
     * This class represents the generalised form of a Cloudonix Profile object.
     * Profiles are used in Cloudonix with various data models - and is free formed.
     *
     * @package cloudonixPhp
     * @file    Entities/Profile.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=tenant
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */
    class Profile extends CloudonixEntity
    {
        public function __construct(mixed $stdObject)
        {
            parent::__construct($stdObject);
        }

        public function getPath(): string
        {
            return "";
        }
    }