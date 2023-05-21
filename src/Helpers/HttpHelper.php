<?php
    /**
     *  ██████╗██╗      ██████╗ ██╗   ██╗██████╗  ██████╗ ███╗   ██╗██╗██╗  ██╗
     * ██╔════╝██║     ██╔═══██╗██║   ██║██╔══██╗██╔═══██╗████╗  ██║██║╚██╗██╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██╔██╗ ██║██║ ╚███╔╝
     * ██║     ██║     ██║   ██║██║   ██║██║  ██║██║   ██║██║╚██╗██║██║ ██╔██╗
     * ╚██████╗███████╗╚██████╔╝╚██████╔╝██████╔╝╚██████╔╝██║ ╚████║██║██╔╝ ██╗
     *  ╚═════╝╚══════╝ ╚═════╝  ╚═════╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═╝╚═╝  ╚═╝
     *
     * @project :  cloudonix-php
     * @filename: HttpHelper.php
     * @author  :   nirs
     * @created :  2023-05-11
     */

    namespace Cloudonix\Helpers;

    use Exception;
    use stdClass;
    use GuzzleHttp\Client as GuzzleClient;
    use GuzzleHttp\Exception\ClientException as GuzzleClientException;
    use GuzzleHttp\Exception\ServerException as GuzzleServerException;
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
        public function __construct(string $apikey, string $endpoint = HTTP_ENDPOINT, float $timeout = HTTP_TIMEOUT, int $debug = DISABLE)
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
         * @param      $method
         * @param      $request
         * @param null $data
         *
         * @return stdClass
         * @throws Exception
         * @throws GuzzleClientException
         * @throws GuzzleServerException
         */
        public function request($method, $request, $data = null): object
        {
            if ($data != null)
                $this->httpHeaders['Content-Type'] = "application/json";

            switch (strtoupper($method)) {
                case "POST":
                    if ($data != null)
                        $requestData = ['headers' => $this->httpHeaders, 'json' => $data];
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
                        $requestData = ['headers' => $this->httpHeaders, 'json' => $data];
                    else
                        $requestData = ['headers' => $this->httpHeaders];
                    $result = $this->connector->request('PUT', $request, $requestData);
                    break;
                case "PATCH":
                    if ($data != null)
                        $requestData = ['headers' => $this->httpHeaders, 'json' => $data];
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