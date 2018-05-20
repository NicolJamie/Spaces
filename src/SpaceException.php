<?php
namespace NicolJamie\Spaces;

abstract class SpaceException
{
    /**
     * command and exceptions
     * @var array
     */
    protected static $exceptions = [
        'create' => ['space'],
        'upload' => [
            'pathToFile',
            'access',
        ],
        'remove' => ['file'],
        'set' => [
            'set',
            'value'
        ],
        'uploadDirectory' => ['directory'],
        'downloadDirectory' => ['directory'],
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

        // Loop and check exceptions
        foreach (self::$exceptions[$command] as $com => $exception) {
            if (!in_array($exception, array_keys($inspect))) {
                throw new \Exception($exception .  ' has not been found in the passed variables');
            }

            if (empty($inspect[$exception])) {
                throw new \Exception($exception .  ' does not contain any data');
            }
        }

        // Upload specific error
        if ($command === 'upload' && !is_bool($inspect['access'])) {
            throw new \Exception('Access needs to be type boolean');
        }
    }
}