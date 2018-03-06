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

        $this->space = $this->bootConnection();
    }

    /**
     * list
     * List all Spaces within the given region
     * @return \Aws\Result|null|string
     */
    public function list($clean = false)
    {
        $this->setSpace(null);

        $list = null;

        try {
            $list = $this->space->listBuckets();
        } catch (S3Exception $exception) {
            return $exception->getMessage();
        }

        return $list;
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