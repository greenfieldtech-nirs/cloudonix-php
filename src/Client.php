<?php
/**
 *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
 * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
 * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
 * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
 *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
 *
 * Project: cloudonix-php | Client.php
 * Creator: nirs | 2019-06-26
 */


namespace Cloudonix;

use Exception;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Opis\Cache\Drivers\File;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;

/**
 * Cloudonix API.Core Client - Command and Control REST Client
 *
 * @package Cloudonix
 */
class Client
{
	/** @var string Directory to store cache files, defaults to `/tmp` */
	public $cacheDirectory = '/tmp';

	/** @var object The Cache Manager Object */
	public $cacheHandler;

	/** @var string The Cloudonix API.Core Endpoint URL */
	public $httpEndpoint = 'https://api.cloudonix.io';

	/** @var string The library identification string */
	public $httpClientIdent = 'cloudonix-php library 0.1';

	/** @var object Previously initiated Cloudonix\Client Object */
	public $handler;

	/** @var object Guzzle HTTP Client Connector */
	public $httpConnector;

	/** @var array HTTP Headers to be used with all Guzzle HTTP Client requests */
	public $httpHeaders;

	/** @var string Cloudonix assigned API key for client init */
	public $apikey;

	/** @var string Cloudonix provisioned Tenant name */
	public $tenantName;

	/** @var integer Cloudonix provisioned Tenant ID */
	public $tenantId;

	/** @var Tenants Cloudonix Tenants REST API Connector */
	protected $tenantsInterface;

	/** @var Domains Cloudonix Domains REST API Connector */
	protected $domainsInterface;

	/** @var Applications Cloudonix Applications REST API Connector */
	protected $applicationsInterface;

	/** @var Subscribers Cloudonix Subscribers REST API Connector */
	protected $subscribersInterface;

	/** @var Trunks Cloudonix Trunks REST API Connector */
	protected $trunksInterface;

	/** @var Dnids Cloudonix Dnids REST API Connector */
	protected $dnidsInterface;

	/**
	 * Client constructor.
	 *
	 * @param string $apikey Cloudonix assigned API key.
	 * @param string $cacheDirectory A designated Cache Memory directory - default '/tmp'
	 * @param string $httpEndpoint An alternative Cloudonix API Endpoint - default 'https://api.cloudonix.io'
	 * @param double $timeout An alternative HTTP timeout value for HTTP requests - default 2.0 seconds
	 *
	 * @throws Exception In case of library init error
	 */
	public function __construct($apikey = null, $httpEndpoint = null, $cacheDirectory = null, $timeout = 2.0)
	{
		try {

			$this->httpEndpoint = (($httpEndpoint != null) && (strlen($httpEndpoint))) ? $httpEndpoint : $this->httpEndpoint;
			$this->cacheDirectory = (($cacheDirectory != null) && (strlen($cacheDirectory))) ? $cacheDirectory : sys_get_temp_dir();
			$this->cacheHandler = new File($this->cacheDirectory);

			$mySanityCheckValue = uniqid("", TRUE);
			$this->cacheHandler->write('mySanityValue', $mySanityCheckValue);
			$mySanityReadValue = $this->cacheHandler->read('mySanityValue');
			if ($mySanityCheckValue != $mySanityReadValue)
				throw new Exception('Cache engine not properly working, bailing out', 500);
			$this->cacheHandler->clear();

			$this->httpConnector = $this->buildHttpClient($apikey, $timeout);

		} catch (Exception $e) {
			die($e->getMessage() . '  code: ' . $e->getCode());
		}
	}

	/**
	 * Client Destructor
	 */
	public function __destruct()
	{
		$this->cacheHandler->clear();
	}

	/**
	 * Build the Client Guzzle HTTP Client
	 *
	 * @param string $apikey Cloudonix assigned API key.
	 * @param double $timeout An alternative HTTP timeout value for HTTP requests
	 * @return GuzzleClient
	 */
	private function buildHttpClient($apikey, $timeout = 2.0):GuzzleClient
	{

		$this->apikey = $apikey;
		$httpConnector = new GuzzleClient([
			'base_uri' => $this->httpEndpoint,
			'timeout' => $timeout,
			'http_errors' => false
		]);

		$this->httpHeaders = [
			'Authorization' => 'Bearer ' . $apikey,
			'User-Agent' => $this->httpClientIdent
		];
		return $httpConnector;
	}

	/**
	 * @return Tenants
	 */
	public function tenants(): Tenants
	{
		if (!$this->tenantsInterface) {
			$this->tenantsInterface = new Tenants($this);
		}
		return $this->tenantsInterface;
	}

	/**
	 * @return Domains
	 */
	public function domains(): Domains
	{
		if (!$this->domainsInterface) {
			$this->domainsInterface = new Domains($this);
		}
		return $this->domainsInterface;
	}

	/**
	 * @return Applications
	 */
	public function applications(): Applications
	{
		if (!$this->applicationsInterface) {
			$this->applicationsInterface = new Applications($this);
		}
		return $this->applicationsInterface;
	}

	/**
	 * @return Dnids
	 */
	public function dnids(): Dnids
	{
		if (!$this->dnidsInterface) {
			$this->dnidsInterface = new Dnids($this);
		}
		return $this->dnidsInterface;
	}

	/**
	 * @return Subscribers
	 */
	public function subscribers(): Subscribers
	{
		if (!$this->subscribersInterface) {
			$this->subscribersInterface = new Subscribers($this);
		}
		return $this->subscribersInterface;
	}

	/**
	 * @return Trunks
	 */
	public function trunks(): Trunks
	{
		if (!$this->trunksInterface) {
			$this->trunksInterface = new Trunks($this);
		}
		return $this->trunksInterface;
	}

	/**
	 * Issue a REST HTTP request to Cloudonix API endpoint - based on provided information
	 *
	 * @param $method
	 * @param $request
	 * @param null $data
	 * @return GuzzleResponse
	 * @throws Exception
	 * @throws GuzzleClientException
	 * @throws GuzzleServerException
	 */
	public function httpRequest($method, $request, $data = null): GuzzleResponse
	{
		if ($data != null)
			$this->httpHeaders['Content-Type'] = "application/json";

		switch (strtoupper($method)) {
			case "POST":
				if ($data != null)
					$requestData = ['headers' => $this->httpHeaders, 'json' => $data];
				else
					$requestData = ['headers' => $this->httpHeaders];
				$result = $this->httpConnector->request('POST', $request, $requestData);
				break;
			case "GET":
				$requestData = ['headers' => $this->httpHeaders];
				$result = $this->httpConnector->request('GET', $request, $requestData);
				break;
			case "DELETE":
				$requestData = ['headers' => $this->httpHeaders];
				$result = $this->httpConnector->request('DELETE', $request, $requestData);
				break;
			case "PUT":
				if ($data != null)
					$requestData = ['headers' => $this->httpHeaders, 'json' => $data];
				else
					$requestData = ['headers' => $this->httpHeaders];
				$result = $this->httpConnector->request('PUT', $request, $requestData);
				break;
			case "PATCH":
				if ($data != null)
					$requestData = ['headers' => $this->httpHeaders, 'json' => $data];
				else
					$requestData = ['headers' => $this->httpHeaders];
				$result = $this->httpConnector->request('PATCH', $request, $requestData);
				break;
			default:
				throw new Exception('HTTP Method request not allowed', 500, null);
				break;
		}

		switch ($result->getStatusCode()) {
			case 204:
			case 200:
				return $result;
				break;
			case 404:
				throw new GuzzleClientException('Resource not found', $result->getStatusCode(), null);
				break;
			case 401:
			case 407:
			case 403:
				throw new GuzzleClientException('Access denied!', $result->getStatusCode(), null);
				break;
			default:
				throw new GuzzleClientException('General error - unspecified', $result->getStatusCode(), null);
				break;
		}
	}

	/**
	 * Get Self information for the provided API key
	 *
	 * @return array
	 */
	public function getSelf(): array
	{
		try {

			$mySelfKeyResult = $this->httpRequest('GET', 'keys/self');
			$myTenantData = json_decode((string)$mySelfKeyResult->getBody());

			/* Store Tenant Information to Cache */
			$this->cacheHandler->write($this->apikey . '-cxTenantId', $myTenantData->tenantId);
			$this->cacheHandler->write($this->apikey . '-cxTenantName', $myTenantData->name);
			$this->cacheHandler->write($this->apikey . '-cxTenantApikey', $myTenantData->keyId);
			$this->cacheHandler->write($this->apikey . '-cxTenantApiSecret', $myTenantData->secret);

			$this->tenantName = $myTenantData->name;
			$this->tenantId = $myTenantData->tenantId;

			$result = [
				'tenant-name' => $this->tenantName,
				'tenant-id' => $this->tenantId,
				'datamodel' => $myTenantData
			];

			return $result;

		} catch (GuzzleServerException $e) {
			die($e->getMessage() . '  code: ' . $e->getCode());
		} catch (GuzzleClientException $e) {
			die($e->getMessage() . '  code: ' . $e->getCode());
		} catch (Exception $e) {
			die($e->getMessage() . '  code: ' . $e->getCode());
		}
	}
}