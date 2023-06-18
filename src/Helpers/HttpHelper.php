<?php
    /**
     * @package cloudonix-php
     * @file    Helpers/HttpHelper.php
     * @author  Nir Simionovich <nirs@cloudonix.io>
     * @license MIT License (https://choosealicense.com/licenses/mit/)
     * @created 2023-05-14
     */

    namespace Cloudonix\Helpers;

    use Exception;
    use GuzzleHttp\Exception\GuzzleException;
    use stdClass;
    use GuzzleHttp\Client as GuzzleClient;

    /**
     * HTTP Helper Class
     *
     * This class is a wrapper for GuzzleHttp which is used extensively in this SDK.
     */
    class HttpHelper
    {
        protected GuzzleClient $connector;
        protected array $httpHeaders;

        /**
         * @param string $apikey
         * @param string $endpoint
         * @param float  $timeout
         * @param int   $debug
         */
        public function __construct(string $apikey, string $endpoint = HTTP_ENDPOINT, float $timeout = HTTP_TIMEOUT, int $debug = LOGGER_DISABLE)
        {
            $this->connector = new GuzzleClient([
                'base_uri' => $endpoint,
                'timeout' => $timeout,
                'http_errors' => false,
                'debug' => !(($debug < 0))
            ]);

            $this->httpHeaders = [
                'Authorization' => 'Bearer ' . $apikey,
                'User-Agent' => HTTP_AGENT
            ];
        }

        /**
         * Issue a REST HTTP request to Cloudonix API endpoint - based on provided information
         *
         * @param string $method
         * @param string $request
         * @param null   $data
         *
         * @return stdClass
         * @throws GuzzleException
         */
        public function request(string $method, string $request, mixed $data = null): object
        {
            $requestBodyType = 'body';
            if (($data != null) && (is_array($data))) {
                $this->httpHeaders['Content-Type'] = "application/json";
                $requestBodyType = 'json';
            }

            switch (strtoupper($method)) {
                case "POST":
                    if ($data != null)
                        $requestData = ['headers' => $this->httpHeaders, $requestBodyType => $data];
                    else
                        $requestData = ['headers' => $this->httpHeaders];
                    $result = $this->connector->request('POST', $request, $requestData);
                    break;
                case "GET":
                    $requestData = ['headers' => $this->httpHeaders];
                    $result = $this->connector->request('GET', $request, $requestData);
                    break;
                case "DELETE":
                    $requestData = ['headers' => $this->httpHeaders];
                    $result = $this->connector->request('DELETE', $request, $requestData);
                    break;
                case "PUT":
                    if ($data != null)
                        $requestData = ['headers' => $this->httpHeaders, $requestBodyType => $data];
                    else
                        $requestData = ['headers' => $this->httpHeaders];
                    $result = $this->connector->request('PUT', $request, $requestData);
                    break;
                case "PATCH":
                    if ($data != null)
                        $requestData = ['headers' => $this->httpHeaders, $requestBodyType => $data];
                    else
                        $requestData = ['headers' => $this->httpHeaders];
                    $result = $this->connector->request('PATCH', $request, $requestData);
                    break;
                default:
                    throw new Exception('HTTP Method request not allowed', 500, null);
                    break;
            }

            switch ($result->getStatusCode()) {
                case 204:
                    $result = ['code' => 204, 'message' => 'No content'];
                    break;
                case 201:
                case 200:
                    $result = json_decode((string)$result->getBody());
                    break;
                case 404:
                    $result = ['code' => 404, 'message' => 'No resource found', 'restResponse' => $result->getBody()->getContents()];
                    break;
                case 401:
                case 407:
                case 403:
                    $result = ['code' => (int)$result->getStatusCode(), 'message' => 'Security violation', 'restResponse' => $result->getBody()->getContents()];
                    break;
                default:
                    $result = ['code' => (int)$result->getStatusCode(), 'message' => 'General Error', 'restResponse' => $result->getBody()->getContents()];
                    break;
            }

            return (object)$result;
        }

    }