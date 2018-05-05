<?php
namespace NicolJamie\Spaces;

abstract class SpaceException
{
    protected static $exceptions = [
        'create' => ['space']
    ];

    /**
     * @param array $inspect
     * @param null $command
     *
     * @throws \Exception
     */
    public static function inspect($inspect = [], $command = null)
    {
        if (is_null($command)) {
            throw new \Exception('Command has not been specified');
        }

        foreach (self::$exceptions as $command => $exception) {
            

        }
    }
}