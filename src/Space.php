<?php

namespace NicolJamie\Spaces;


use Aws\S3\Exception\S3Exception;

class Space extends Affix
{
    /**
     * instance
     * @var null
     */
    public static $instance = null;

    /**
     * space
     * @var \Aws\S3\S3Client|string
     */
    public $space;

    /**
     * Space constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list
     * List all Spaces within the given region
     * @return \Aws\Result|null|string
     */
    public function list($clean = false)
    {
        $list = null;

        try {
            $list = $this->bootConnection()->listBuckets();
        } catch (S3Exception $exception) {
            return $exception->getMessage();
        }

        return $list->toArray();
    }

    /**
     * create
     * Create a new space within the region
     * @param null $space
     *
     * @return array|string
     * @throws \Exception
     */
    public function create($space = null)
    {
        if (is_null($space)) {
            throw new \Exception('no space name has been set, please set one');
        }

        $this->setSpace($space);

        $connection = $this->bootConnection();

        try {
            $new = $connection->createBucket([
                'Bucket' => $space
            ]);

            $connection->waitUntil('BucketExists', [
                'Bucket' => $space
            ]);

        } catch (S3Exception $exception) {
            return $exception->getMessage();
        }

        return $new;
    }

    /**
     * boot
     * @return Space
     * @throws \Exception
     */
    public static function boot()
    {
        $instance = self::$instance;

        if (is_null($instance)) {
            $instance = self::$instance = new self();
        }

        return $instance;
    }
}