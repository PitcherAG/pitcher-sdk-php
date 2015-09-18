<?php

namespace Pitcher\ZeroDrive;

use Pitcher\PitcherClient;

/**
 * Client used to interact with **Pitcher ZeroDrive**.
 */
class ZeroDriveClient extends PitcherClient
{
    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['endpoint'] = 'https://zerodrive.pitcher.com';

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
     * - thumbnails: (bool) Set to true to receive thumbnail urls. Default true.
     * - order: (bool) Set to true to receive slide order. Default true.
     * - chapters: (bool) Set to true to receive chapters. Default true.
     * - files: (array) Get additional file URLs
     *
     * @param array $args
     * @return Promise\PromiseInterface
     */
    public function getSlidesAsync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("ID", $args))
            throw new \InvalidArgumentException("Missing argument: ID");

        // set defaults
        $conf = array_merge_recursive([
            "thumbnails" => true,
            "order" => true,
            "chapters" => true
        ], $args);

        return $this->requestAsync("GET", "/api/slides/$conf[ID]");
    }

    /**
     * Get the basic information about a slideshow. 
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     * - thumbnails: (bool) Set to true to receive thumbnail urls. Default true.
     * - order: (bool) Set to true to receive slide order. Default true.
     * - chapters: (bool) Set to true to receive chapters. Default true.
     * - files: (array) Get additional file URLs
     *
     * @param array $args
     * @return array
     */
    public function getSlides(array $args)
    {
        return $this->getSlidesAsync($args)->wait();
    }
}