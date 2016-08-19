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
     * @var string
     */
    protected $bucket;

    /**
     * @author Krzysztof Bednarczyk
     * S3StorageProvider constructor.
     */
    public function __construct(S3Client $client, $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
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
        $this->client->putObject(array(
            'Bucket' => $this->bucket,
            'Key' => $this->getKey($bucket, $id),
            'Body' => $resource,
            'ACL' => 'public-read'
        ));

        return true;
    }

    /**
     * @param string $bucket
     * @param string $id
     * @return string
     * @author Krzysztof Bednarczyk
     */
    public function getKey($bucket, $id)
    {
        $cat = substr(md5($id), 0, 3);
        return "{$bucket}/{$cat}/{$id}.file";
    }

    /**
     * @param string $bucket
     * @param string $id
     * @return resource
     * @author Krzysztof Bednarczyk
     */
    public function fetch($bucket, $id)
    {
        $result = $this->client->getObject(array(
            'Bucket' => $this->bucket,
            'Key' => $this->getKey($bucket, $id)
        ));
    }

    /**
     * @param string $bucket
     * @param string $id
     * @return mixed
     * @author Krzysztof Bednarczyk
     */
    public function remove($bucket, $id)
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $this->getKey($bucket, $id)
        ]);

        return true;
    }

    /**
     * @param string $bucket
     * @param string $id
     * @return boolean
     * @author Krzysztof Bednarczyk
     */
    public function exist($bucket, $id)
    {
        try {
            $info = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getKey($bucket, $id)
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return $info ? true : null;
    }
}