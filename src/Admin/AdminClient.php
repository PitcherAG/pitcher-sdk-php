<?php

namespace Pitcher\Admin;

use Pitcher\PitcherClient;

/**
 * Client used to interact with **Pitcher ZeroDrive**.
 */
class AdminClient extends PitcherClient
{
    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['endpoint'] = 'https://admin.pitcher.com';

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * In addition to the options available to
     * {@see Pitcher\PitcherClient::__construct}, ZeroDriveClient accepts the following
     * options:
     *
     * @param array $args
     */
    public function __construct(array $args)
	{
        parent::__construct($args);
	}

    /**
     * Get the basic information about a slideshow. 
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     *
     * @param array $args
     * @return Promise\PromiseInterface
     */
    public function getFileUrlAsync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("ID", $args))
            throw new \InvalidArgumentException("Missing argument: ID");

        // set defaults
        $conf = array_merge([], $args);

        return $this->requestAsync("GET", "/api/file/url", ["query" => ["ID" => $conf["ID"]]]);
    }

    /**
     * Get the basic information about a slideshow. 
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     *
     * @param array $args
     * @return array
     */
    public function getFileUrl(array $args)
    {
        return $this->getFileUrlAsync($args)->wait();
    }
}