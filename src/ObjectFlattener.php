<?php
namespace Aspyct\MongoMap;

require_once __DIR__ . '/Manipulator.php';

class ObjectFlattener extends Manipulator {
    public function flatten($object, $exportClass=true) {
        $result = array();
        
        if ($exportClass) {
            $result[$this->getClassTag()] = get_class($object);
        }
        
        $class = new \ReflectionClass($object);
        foreach ($class->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            
            if (is_object($value)) {
                $value = $this->flatten($value, $exportClass);
            }
            
            $result[$property->getName()] = $value;
        }
        
        return $result;
    }
}
