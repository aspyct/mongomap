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

/** Object to array converter
 * 
 * An object of this class is capable of converting PHP objects to simple
 * arrays, that can then be stored into a MongoDB (or sent via Ajax, or...).
 * If required, the class name is exported with the properties, so that
 * the object can be deserialized later.
 * 
 */
class ObjectFlattener extends Manipulator {
    const GETTER_PATTERN = '#(?:is|get)([A-Z][a-zA-Z0-9_]*)#';

    const BASED_ON_GETTERS = 1 << 0;
    const BASED_ON_PROPERTIES = 1 << 1;

    public function flatten($object, $exportClass=true, $base=self::BASED_ON_GETTERS) {
        $result = array();
        
        if ($exportClass) {
            $result[$this->getClassTag()] = get_class($object);
        }
        
        $class = new \ReflectionClass($object);

        if ($base & self::BASED_ON_GETTERS) {
            foreach ($class->getMethods() as $method) {
                if (preg_match(self::GETTER_PATTERN, $method->getName(), $match)) {
                    $result[lcfirst($match[1])] = $method->invoke($object);
                }
            }
        }

        if ($base & self::BASED_ON_PROPERTIES) {
            foreach ($class->getProperties() as $property) {
                if (!array_key_exists($property->getName(), $result)) {
                    $property->setAccessible(true);
                    $value = $property->getValue($object);
                    
                    if (is_object($value)) {
                        $value = $this->flatten($value, $exportClass);
                    }
                    
                    $result[$property->getName()] = $value;
                }
            }
        }
        
        return $result;
    }
}
