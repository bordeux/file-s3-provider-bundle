<?php

namespace Bordeux\Bundle\FileS3ProviderBundle\Storage\Factory;


use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Bordeux\Bundle\FileBundle\Entity\Storage;
use Bordeux\Bundle\FileBundle\Storage\StorageFactory;
use Bordeux\Bundle\FileBundle\Storage\StorageProvider;
use Bordeux\Bundle\FileS3ProviderBundle\Storage\Provider\S3StorageProvider;

/**
 * Class S3Factory
 * @package Bordeux\Bundle\FileS3ProviderBundle\Storage\Factory
 * @author Krzysztof Bednarczyk
 */
class S3Factory implements StorageFactory
{

    /**
     * @var S3Client[]
     */
    protected $clients = [];

    /**
     * @param Storage $storage
     * @return StorageProvider
     * @author Krzysztof Bednarczyk
     */
    public function getProvider(Storage $storage)
    {
        return new S3StorageProvider(
            $this->getAwsClient($storage)
        );
    }


    /**
     * @param array $params
     * @author Krzysztof Bednarczyk
     */
    public function validParameters(array $params){
        //@todo: check parameters and throw exceptions
    }

    /**
     * @param Storage $storage
     * @return S3Client
     * @author Krzysztof Bednarczyk
     */
    public function getAwsClient(Storage $storage)
    {
        if (isset($this->clients[$storage->getId()])) {
            return $this->clients[$storage->getId()];
        }


        $params = $storage->getParametersArray();

        $this->validParameters($params);

        $client = new S3Client(array(
            'credentials' => new Credentials(
                $params['access_key'],
                $params['secret_key']
            ),
            'region' => $params['region'],
            'bucket' => $params['bucket']
        ));

        $this->clients[$storage->getId()] = $client;

        return $client;
    }
}