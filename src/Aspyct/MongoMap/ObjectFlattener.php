<?php

namespace Aspyct\MongoMap;

require_once __DIR__ . '/Manipulator.php';

class ObjectFlattener extends Manipulator
{

    public function flatten($data, $exportClass = true)
    {

        // Not an object nor an array, we return the single value
        if (!is_object($data) && !is_array($data))
        {
            return $data;
        }

        $result = array();

        // Adding class name to export if asked to and data is an object
        if ($exportClass && is_object($data))
        {
            $result[$this->getClassTag()] = get_class($data);
        }

        // If it's an object, we determine properties and recurse
        if (is_object($data))
        {

            $class = new \ReflectionClass($data);
            foreach ($class->getProperties() as $property)
            {
                $property->setAccessible(true);
                $result[$property->getName()] = $this->flatten($property->getValue($data));
            }
        }
        // It's an array
        else
        {
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->flatten($value);
            }
        }


        return $result;
    }

}
