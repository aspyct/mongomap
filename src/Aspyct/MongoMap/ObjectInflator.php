<?php

/*
 * Copyright (c) 2012 Antoine d'Otreppe de Bouvette <aob@emakina.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace Aspyct\MongoMap;

require_once __DIR__ . '/Manipulator.php';

/** Array to object converter
 *
 * An object of this class is capable of deserializing data coming
 * from an ObjectFlattener.
 *
 * First we try to detect the class tag in the given array. If it's found,
 * an object of this class is created.
 *
 * For each property found in the array, we try to set it on the object.
 * We try to set the property via three different ways, in the order:
 *   1. via the corresponding setter, if it exists
 *   2. set the property directly, if it exists
 *   3. create a public property on the object
 *
 */
class ObjectInflator extends Manipulator
{

    public function inflate($data)
    {
        // It's not an array, let's return raw data
        if (!is_array($data))
        {
            return $data;
        }

        // It's an array.
        else
        {

            // It's not a class serialized by an ObjectFlattener
            // Let's recurse to see if it contains classes.
            if (!array_key_exists($this->getClassTag(), $data))
            {
                $return = array();
                foreach ($data as $property => $value)
                {
                    $return[$property] = $this->inflate($value);
                }
                return $return;
            }

            // PHP class name is provided, let's inflate the object
            else
            {
                // Get the class and remove it from the array
                $className = $data[$this->getClassTag()];
                unset($data[$this->getClassTag()]);

                $class = new \ReflectionClass($className);
                $object = $class->newInstance();

                // Set the properties of the object
                foreach ($data as $property => $value)
                {
                    $setter = 'set' . ucfirst($property);
                    $value = $this->inflate($value);

                    // There are three ways to set a property
                    // 1. via the corresponding setter method
                    if (method_exists($object, $setter))
                    {
                        $object->$setter($value);
                    }
                    // 2. via the property if it exists
                    else if ($class->hasProperty($property))
                    {
                        $accessor = $class->getProperty($property);
                        $accessor->setAccessible(true);
                        $accessor->setValue($object, $value);
                    }
                    // 3. set a public property otherwise
                    else
                    {
                        $object->$property = $value;
                    }
                }
            }

            return $object;
        }
    }

}