<?php
    namespace Cloudonix\Collections;

    use Cloudonix\CXClient as CXClient;
    use Cloudonix\Collections\CloudonixCollection as CloudonixCollection;

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
     * @file    Collections/Tenants.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @created 2023-05-09
     */

    class Tenants extends CloudonixCollection
    {

        public function __construct(CXClient $client)
        {
            parent::__construct($this->refreshCollectionData($client));
        }

        public function getPath(): string
        {
            return BASEURL_TENANTS;
        }

        protected function refreshCollectionData(mixed $param): array {

        }
    }