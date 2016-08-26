<?php
namespace Bordeux\Bundle\FileS3ProviderBundle\Storage\Provider;

use Aws\S3\S3Client;
use Bordeux\Bundle\FileBundle\Exception\Provider\FileNotFoundException;
use Bordeux\Bundle\FileBundle\Storage\StorageProvider;

class S3StorageProvider extends StorageProvider
{
    /**
     * @var S3Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @author Krzysztof Bednarczyk
     * S3StorageProvider constructor.
     * @param S3Client $client
     * @param string $bucket
     * @param string $dir
     */
    public function __construct(S3Client $client, string $bucket, string $dir)
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->dir = $dir;
    }


    /**
     * @param string $bucket
     * @param string $id
     * @param resource $resource
     * @return boolean
     * @author Krzysztof Bednarczyk
     */
    public function put(int $id, \resource $resource) : bool
    {
        try {
            $this->client->putObject(array(
                'Bucket' => $this->bucket,
                'Key' => $this->getKey($id),
                'Body' => $resource,
                'ACL' => 'public-read'
            ));
        } catch (\Aws\S3\Exception\S3Exception $e) {
            if ($e->getAwsErrorCode() === 'NoSuchBucket') {
                $this->createBucket();
                return $this->put( $id, $resource);
            }
            throw $e;
        }

        return true;
    }


    /**
     * @return bool
     * @author Krzysztof Bednarczyk
     */
    protected function createBucket()
    {

        $this->client->createBucket([
            'Bucket' => $this->bucket
        ]);

        return true;
    }

    /**
     * @param string $bucket
     * @param int $id
     * @return string
     * @author Krzysztof Bednarczyk
     */
    public function getKey(int $id)
    {
        $cat = ceil($id/1000);
        return "{$this->dir}/{$cat}/{$id}.file";
    }

    /**
     * @param string $id
     * @return resource
     * @author Krzysztof Bednarczyk
     */
    public function fetch(int $id) : \resource
    {
        try {
            $result = $this->client->getObject(array(
                'Bucket' => $this->bucket,
                'Key' => $this->getKey($id)
            ));

            /** @var \GuzzleHttp\Psr7\Stream $body */
            $body = $result['Body'];

        } catch (\Aws\S3\Exception\S3Exception $e) {
            if ($e->getAwsErrorCode() === 'NoSuchKey') {
                throw new FileNotFoundException(
                    $e->getMessage()
                );
            }
            throw $e;
        }


        return $body->detach();
    }

    /**
     * @param int $id
     * @return bool
     * @author Krzysztof Bednarczyk
     */
    public function remove(int $id) : bool
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $this->getKey($id)
        ]);

        return true;
    }

    /**

     * @param string $id
     * @return boolean
     * @author Krzysztof Bednarczyk
     */
    public function exist(int $id) : bool
    {
        try {
            $info = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getKey($id)
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return $info ? true : false;
    }
}