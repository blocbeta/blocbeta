<?php

namespace App\Factory;

class RedisConnectionFactory
{
    public const DB_DEFAULT = 0;
    public const DB_TRACKING = 1;

    public static function create()
    {
        $redis = new \Redis();
        $redis->connect($_ENV["REDIS_HOST"]);

        return $redis;
    }

    public static function explodeKey(string $key): array
    {
        $parts = explode(":", $key);
        $data = [];

        foreach ($parts as $part) {
            if (strpos($part, '=') === false) {
                continue;
            }

            $value = explode("=", $part);
            $data[$value[0]] = $value[1];
        }

        return $data;
    }

}