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
     * Upload a file
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     * - file: (string) Path to zip packaged file.
     *
     * @param array $args
     * @return Promise\PromiseInterface
     */
    public function putFileAsync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("ID", $args))
            throw new \InvalidArgumentException("ID");

        // required arguments
        if (array_key_exists("file", $args) && (!is_file($args["file"]) || false === ($resource = fopen('http://httpbin.org', 'r'))))
            throw new \InvalidArgumentException("file");

        // set defaults
        $conf = array_merge([], $args);
		if(array_key_exists("file", $args)){
	        return $this->requestAsync("PUT", "/api/extracted-file/upload", [
	            'query' => "ID=$args[ID]",
	            'body' => fopen($args["file"], 'r')
	        ]);
		}
		else{
	        return $this->requestAsync("PUT", "/api/extracted-file/upload", [
	            'query' => "ID=$args[ID]"
	        ]);
		}
       
    }
    /**
     * Upload a file
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     * - file: (string) Path to zip packaged file.
     *
     * @param array $args
     * @return Promise\PromiseInterface
     */
    public function putFileSync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("ID", $args))
            throw new \InvalidArgumentException("ID");

        // required arguments
        if (array_key_exists("file", $args) && (!is_file($args["file"]) || false === ($resource = fopen('http://httpbin.org', 'r'))))
            throw new \InvalidArgumentException("file");

        // set defaults
        $conf = array_merge([], $args);
		if(array_key_exists("file", $args)){
	        return $this->requestSync("PUT", "/api/extracted-file/upload", [
	            'query' => "ID=$args[ID]",
	            'body' => fopen($args["file"], 'r')
	        ]);
		}
		else{
	        return $this->requestSync("PUT", "/api/extracted-file/upload", [
	            'query' => "ID=$args[ID]"
	        ]);
		}
       
    }

    /**
     * Upload a file
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     * - file: (string) Path to zip packaged file.
     *
     * @param array $args
     * @return void
     */
    public function putFile(array $args)
    {
		return $this->putFileAsync($args)->wait();
		//return $this->putFileSync($args);
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
    public function getMetadataAsync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("ID", $args))
            throw new \InvalidArgumentException("Missing argument: ID");

        // set defaults
        $conf = array_merge([], $args);

        return $this->requestAsync("GET", "/api/metadata/$conf[ID]");
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
    public function getMetadata(array $args)
    {
        return $this->getMetadataAsync($args)->wait();
    }

    /**
     * Get the basic information about a slideshow. 
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     * - metadata: (object) File Metadata.
     *
     * @param array $args
     * @return Promise\PromiseInterface
     */
    public function putMetadataAsync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("ID", $args))
            throw new \InvalidArgumentException("Missing argument: ID");

        // required arguments
        if (!array_key_exists("metadata", $args))
            throw new \InvalidArgumentException("Missing argument: metadata");

        // set defaults
        $conf = array_merge([], $args);
        return $this->requestAsync("PUT", "/api/metadata/$conf[ID]", [ 
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($conf["metadata"]),
        ]);
    }

    public function putMetadataSync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("ID", $args))
            throw new \InvalidArgumentException("Missing argument: ID");

        // required arguments
        if (!array_key_exists("metadata", $args))
            throw new \InvalidArgumentException("Missing argument: metadata");

        // set defaults
        $conf = array_merge([], $args);

        return $this->requestSync("PUT", "/api/metadata/$conf[ID]", [ 
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($conf["metadata"]),
        ]);
    }
    /**
     * Get the basic information about a slideshow. 
     *
     * Accepts the following options:
     *
     * - ID: (string) File ID. Required.
     * - metadata: (object) File Metadata.
     *
     * @param array $args
     * @return array
     */
    public function putMetadata(array $args)
    {
        return $this->putMetadataAsync($args)->wait();
		//return $this->putMetadataSync($args);
    }

    /**
     * Get the basic information about a slideshow. 
     *
     * Accepts the following options:
     *
     * - fileID: (string) File ID. Required.
     * - contents: (array) Paths to be included in package content.
     *
     * @param array $args
     * @return Promise\PromiseInterface
     */
    public function getRepackagedFileAsync(array $args)
    {
        if (!$args)
            throw new \InvalidArgumentException();

        // required arguments
        if (!array_key_exists("fileID", $args))
            throw new \InvalidArgumentException("Missing argument: fileID");

        // required arguments
        if (!array_key_exists("contents", $args))
            throw new \InvalidArgumentException("Missing argument: contents");

        // set defaults
        $conf = array_merge([], $args);

        return $this->requestAsync("POST", "/api/repackaged-file/get", 
            [ 
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($conf),
            ]
        );
    }

    /**
     * Get the basic information about a slideshow. 
     *
     * Accepts the following options:
     *
     * - fileID: (string) File ID. Required.
     * - contents: (array) Paths to be included in package content.
     *
     * @param array $args
     * @return array
     */
    public function getRepackagedFile(array $args)
    {
        return $this->getRepackagedFileAsync($args)->wait();
    }
}
