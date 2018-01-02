<?php

namespace member_eventconsumer\subscriber\middleware\plugin;

/**
 * Salesforce对接接口
 *
 * Class Salesforce
 * @package member_eventconsumer\subscriber\middleware\plugin
 */
class Salesforce extends APIAbstract {
    public
        $last_response;
    protected
        $client_id,
        $client_secret,
        $user_name,
        $password,
        $security_token,
        $login_url,
        $base_url,
        $base_path = '/services/apexrest',
        $headers,
        $return_type;
    private
        $access_token,
        $last_request_uri,
        $handle;

    const
        METH_DELETE = 'DELETE',
        METH_GET    = 'GET',
        METH_POST   = 'POST',
        METH_PUT    = 'PUT',
        METH_PATCH  = 'PATCH';

    // Return types
    const
        RETURN_OBJECT  = 'object',
        RETURN_ARRAY_K = 'array_k',
        RETURN_ARRAY_A = 'array_a';

    const
        LOGIN_PATH   = '/services/oauth2/token',
        GRANT_TYPE  = 'password';

    public function __construct($login_url, $client_id, $client_secret, $user_name, $password, $security_token = '')
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->login_url = $login_url;
        $this->user_name = $user_name;
        $this->password = $password;
        $this->security_token = $security_token;

        $this->return_type = self::RETURN_OBJECT;

        $this->headers = [
            'Content-Type' => 'application/json'
        ];

        if(is_null($this->handle)) {
            $this->handle = curl_init();
            $options = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 240,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_BUFFERSIZE => 128000

            ];
            curl_setopt_array($this->handle, $options);
        }
    }

    public function setBasePath($basePath)
    {
        $this->base_path = $basePath;
    }

    /**
     * Get a list of all the API Versions for the instance
     *
     * @return mixed
     * @throws SalesforceAPIException
     */
    public function getAPIVersions()
    {
        return $this->httpRequest( $this->base_url . '/services/data' );
    }

    /**
     * Get请求
     *
     * @param string $path path中不要有?，参数通过$params传递
     * @param array|null $params
     * @return mixed
     * @throws SalesforceAPIException
     */
    public function get($path, $params = [])
    {
        return $this->request($path, $params, self::METH_GET );
    }

    /**
     * post请求
     * @param $path
     * @param array $params
     * @return mixed
     * @throws SalesforceAPIException
     */
    public function post($path, $params = [])
    {
        return $this->request($path, $params, self::METH_POST);
    }

    public function lastRequestUri()
    {
        return $this->last_request_uri;
    }

    /**
     * Logs in the user to Salesforce with a username, password, and security token
     * @return void
     * @throws SalesforceAPIException
     */
    protected function login()
    {
        $login_data = [
            'grant_type' => self::GRANT_TYPE,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->user_name,
            'password' => $this->password . $this->security_token
        ];

        $ch = curl_init();

        $http_params = http_build_query($login_data);
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8'
        ];

        curl_setopt($ch, CURLOPT_URL, $this->login_url . self::LOGIN_PATH);
        curl_setopt($ch, CURLOPT_POST, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $http_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $login = @json_decode(curl_exec($ch));
        curl_close($ch);

        if (!$login) {
            throw new SalesforceAPIException('login error.');
        }

        $this->access_token = $login->access_token;
        $this->base_url = $login->instance_url;
    }

    /**
     * Makes a request to the API using the access key
     *
     * @param string $path The path to use for the API request
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return mixed
     * @throws SalesforceAPIException
     */
    protected function request($path, $params = [], $method = self::METH_GET, $headers = [])
    {
        if(!isset($this->access_token)) {
            $this->login();

            if (!isset($this->access_token)) {
                throw new SalesforceAPIException('You have not logged in yet.');
            }
        }

        $request_headers = [
            'Authorization' => 'Bearer ' . $this->access_token
        ];

        $request_headers = array_merge($request_headers, $headers);

        return $this->httpRequest($this->base_url . '/' . ltrim($this->base_path, '/') . '/' . trim($path, '/') . '/', $params, $request_headers, $method);
    }

    /**
     * Performs the actual HTTP request to the Salesforce API
     *
     * @param string $url
     * @param array|null $params
     * @param array|null $headers
     * @param string $method
     * @return array
     * @throws SalesforceAPIException
     */
    protected function httpRequest($url, $params = null, $headers = null, $method = self::METH_GET)
    {
        $url = rtrim($url, '/');

        if(isset($headers) && $headers !== null && !empty($headers))
            $request_headers = array_merge($this->headers,$headers);
        else
            $request_headers = $this->headers;

        if(isset($params) && $params !== null && !empty($params)) {
            if($request_headers['Content-Type'] == 'application/json') {
                $json_params = json_encode($params);
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $json_params);
            } else {
                $http_params = http_build_query($params);
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $http_params);
            }
        }

        switch($method)
        {
            case 'POST':
                curl_setopt($this->handle, CURLOPT_POST, true);
                break;
            case 'GET':
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, []);
                curl_setopt($this->handle, CURLOPT_POST, false);
                if(isset($params) && $params !== null && !empty($params))
                    $url .= '?' . http_build_query($params);
                break;
            default:
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        curl_setopt($this->handle, CURLOPT_URL, $url);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->createCurlHeaderArray($request_headers));

        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, false);

        $this->last_request_uri = $url;

        return $this->checkForRequestErrors(curl_exec($this->handle), $this->handle);
    }

    /**
     * Makes the header array have the right format for the Salesforce API
     *
     * @param $headers
     * @return array
     */
    private function createCurlHeaderArray($headers) {
        $curl_headers = [];
        // Create the header array for the request
        foreach($headers as $key => $header) {
            $curl_headers[] = $key . ': ' . $header;
        }
        return $curl_headers;
    }

    /**
     * Checks for errors in a request
     *
     * @param string $response The response from the server
     * @param Resource $handle The CURL handle
     * @return array The response from the API
     * @throws SalesforceAPIException
     * @see http://www.salesforce.com/us/developer/docs/api_rest/index_Left.htm#CSHID=errorcodes.htm|StartTopic=Content%2Ferrorcodes.htm|SkinName=webhelp
     */
    private function checkForRequestErrors($response, $handle) {
        $curl_error = curl_error($handle);
        if($curl_error !== '') {
            throw new SalesforceAPIException($curl_error);
        }
        $request_info = curl_getinfo($handle);

        switch($request_info['http_code']) {
            case 304:
                if($response === '')
                    $response = ['message' => 'The requested object has not changed since the specified time'];
                break;
            case 300:
            case 200:
            case 201:
            case 204:
                if($response === '')
                    $response = ['success' => true];
                break;
            default:
                if(empty($response) || $response !== '')
                    throw new SalesforceAPIException($response);
                else {
                    $response = json_decode($response, true);
                    if(isset($response['error']))
                        throw new SalesforceAPIException($response['error_description']);
                }
                break;
        }

        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        //加入http状态码
        $response['http_code'] = $request_info['http_code'];

        return $response;
    }


}

abstract class APIAbstract {
    /**
     * Converts objects returned into arrays.
     * This is necessary when returning complex objects.
     * For example, an object returned from a search using a cross-object reference cannot be displayed using methods to display simple objects...
     *   /api/task/search?fields=project:name
     *   /api/task/search?fields=DE:Parameter Name
     * both contain colons, which will result in a stdClass error when using the methods to reference simple objects.
     * The function below provides a way to convert the 'project:name' object into a usuable array
     *   i.e. $task['project:name'] can be used by placing the returned object into the function
     *
     */
    function objectToArray ( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map( array($this, 'objectToArray'), $object );
    }
}

class SalesforceAPIException extends \Exception {}