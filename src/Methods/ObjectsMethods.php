<?php

/*
 * This file is part of Underscore.php
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Underscore\Methods;

/**
 * Methods to manage objects.
 */
class ObjectsMethods extends CollectionMethods
{
    /**
     * Get all methods from an object.
     *
     * @param $object
     * @return array
     */
    public static function methods($object)
    {
        return get_class_methods(get_class($object));
    }

    /**
     * Unpack an object's properties.
     *
     * @param $object
     * @param mixed $attribute
     * @return object
     */
    public static function unpack($object, $attribute = null)
    {
        $object = (array) $object;
        $object = $attribute
            ? ArraysMethods::get($object, $attribute)
            : ArraysMethods::first($object);

        return (object) $object;
    }
}
