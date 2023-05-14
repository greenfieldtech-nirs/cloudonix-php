<?php
    namespace Cloudonix\DataModel;

    use Cloudonix\Client as CloudonixClient;

    use Cloudonix\DataModel\Commons\CloudonixCollection;
    use Cloudonix\DataModel\Commons\CloudonixClientObject;

    use Cloudonix\DataModel\Domains as CloudonixDomains;

    use Cloudonix\DataModel\Entities\Domain as CloudonixDomain;
    use Cloudonix\DataModel\Entities\Tenant as CloudonixTenant;

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
     * Tenant provides an interface to the Cloudonix Tenant DataModel.
     * The Tenant DataModel is Cloudonix's primary data model, to which all other
     * data models are related - such as Domain, Subscriber, Application and more.
     *
     * Example Usage:
     *
     * ```
     * use Cloudonix\CloudonixClient;
     *
     * $myCloudonixClient = new CloudonixClient("my.super.secret.API.key");
     * $myTenantObject = $myCloudonixClient->tenant();
     * var_dump($myTenantObject->listDomains());
     * ```
     *
     * @package cloudonixPhp
     * @file    DataModel/Tenant.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @see     https://dev.docs.cloudonix.io/#/platform/api-core/models?id=tenant
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-09
     */
    class Tenant extends CloudonixClientObject
    {
        protected CloudonixTenant $self;

        /**
         * @var CloudonixCollection An instance of a Cloudonix Domains Collection
         */
        private CloudonixCollection $domainsRemote;
        private CloudonixCollection $domainsLocal;

        /**
         * @var string The Cloudonix REST API Canonical path
         */
        private string $modelBasePath = "tenants";

        /**
         * Tenant DataModel Object Constructorß
         *
         * @param CloudonixClient $client A CloudonixClient Object
         * @param string          $name   A Tenant name or numeric ID (treated as string)
         */
        public function __construct(CloudonixClient $client, string $name = "self")
        {
            parent::__construct($client);
            $this->name = $name;
        }

        /**
         * Return the data model associated canonical path
         *
         * @return string
         */
        public function getPath(): string
        {
            return $this->modelBasePath . "/" . $this->name;
        }

        public function getDomains(): CloudonixCollection
        {
            $domainsObject = new CloudonixDomains($this->client, $this->name);
            return $domainsObject->list();
        }

        public function getDomain(string $domain): CloudonixDomain
        {
            $domainsObject = new CloudonixDomains($this->client, $this->name);
            $loadDomainObject = $domainsObject->getDomain($domain);
            return new CloudonixDomain($loadDomainObject, $this->client);
        }

        public function createDomain(string $domain): CloudonixDomain
        {
            $domainsObject = new CloudonixDomains($this->client, $this->name);
            $loadDomainObject = $domainsObject->createDomain($domain);
            return new CloudonixDomain($loadDomainObject, $this->client);
        }

        /**
         * Return the object after assigning values to specific object properties
         *
         * @return CloudonixTenant
         */
        public function getTenant(): CloudonixTenant
        {
            $tenantData = $this->client->httpConnector->request('GET', $this->getPath());
            return new CloudonixTenant($tenantData);
        }

        public function __set(string $name, mixed $value)
        {
            $this->$name = $value;
        }

        public function __toString(): string
        {
            return json_encode($this);
        }

        public function __get(string $name): mixed
        {
            return $this->$name;
        }

    }