<?php
namespace Bordeux\Bundle\FileS3ProviderBundle\Storage\Provider;

use Aws\S3\S3Client;
use Bordeux\Bundle\FileBundle\Storage\StorageProvider;

class S3StorageProvider extends StorageProvider
{
    /**
     * @var S3Client
     */
    protected $client;

    /**
     * @author Krzysztof Bednarczyk
     * S3StorageProvider constructor.
     */
    public function __construct(S3Client $client)
    {
        $this->client = $client;
    }


    /**
     * @param string $bucket
     * @param string $id
     * @param resource $resource
     * @return boolean
     * @author Krzysztof Bednarczyk
     */
    public function put($bucket, $id, $resource)
    {
        // TODO: Implement put() method.
    }

    /**
     * @param string $bucket
     * @param string $id
     * @return resource
     * @author Krzysztof Bednarczyk
     */
    public function fetch($bucket, $id)
    {
        // TODO: Implement fetch() method.
    }

    /**
     * @param string $bucket
     * @param string $id
     * @return mixed
     * @author Krzysztof Bednarczyk
     */
    public function remove($bucket, $id)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @param string $bucket
     * @param string $id
     * @return boolean
     * @author Krzysztof Bednarczyk
     */
    public function exist($bucket, $id)
    {
        // TODO: Implement exist() method.
    }
}