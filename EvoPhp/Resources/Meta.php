<?php

namespace EvoPhp\Resources;

Trait Meta
{

    public static function merge(array|object $data) {
        $method = gettype($data)."Merge";
        return self::$method($data);
    }

    public static function objectMerge($data) {
        return (object) self::arrayMerge((array) $data);
    }

    public static function arrayMerge($data) {
        if(isset($data["meta"])) {
            $meta = (array) json_decode($data["meta"]);
            unset($data["meta"]);
        } else {
            $meta = [];
        }
        return array_merge($meta, $data);
    }
}
