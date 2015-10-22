<?php

namespace Pitcher;

use GuzzleHttp;
use GuzzleHttp\Psr7\Uri;

/**
 * Default Pitcher client implementation
 */
class PitcherClient
{
    /** @var string */
    protected $endpoint;

    /** @var array*/
    protected $credentials;

    /** @var array*/
    protected $headers;

    /**
     * Get an array of client constructor arguments used by the client.
     *
     * @return array
     */
    public static function getArguments()
    {
        return [        
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];
    }

    /**
     * The client constructor accepts the following options:
     *
     * - credentials: (array) Specifies
     *   the credentials used to sign requests. Provide an associative array of
     *   "key", "secret".
     * - endpoint: (string) The full URI of the webservice. This is only
     *   required when connecting to a custom endpoint (e.g., a local version
     *   of ZeroDrive).
     *
     * @param array $args Client configuration arguments.
     *
     * @throws \InvalidArgumentException if any required options are missing or
     *                                   the service is not supported.
     */
    public function __construct(array $args)
    {
        $conf = array_replace_recursive(static::getArguments(), $args);
        $this->endpoint = new Uri($conf["endpoint"]);
        $this->credentials = $conf["credentials"];
        $this->headers = $conf["headers"];
    }

    public function requestAsync($method, $path, array $options = [])
    {
        $client = new GuzzleHttp\Client();

        $promise = $client->requestAsync($method, $this->endpoint->withPath($path), array_replace_recursive([
            'auth' => [$this->credentials["key"], $this->credentials["secret"]],
            'headers' => $this->headers,
        ], $options))->then(function($res) {
            return json_decode($res->getBody(), true);
        });

        return $promise;
    }

    public function request($method, $path, array $options = [])
    {
        return $this->requestAsync($method, $path, $options)->wait();
    }
}