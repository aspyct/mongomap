<?php
namespace Aspyct\MongoMap;

require_once __DIR__ . '/Manipulator.php';

class ObjectInflator extends Manipulator {
    public function inflate(array $data) {
        if (array_key_exists($this->getClassTag(), $data)) {
            $className = $data[$this->getClassTag()];
            unset($data[$this->getClassTag()]);
            
            $class = new \ReflectionClass($className);
            $object = $class->newInstance();
            
            foreach ($data as $property=>$value) {
                $setter = 'set'.ucfirst($property);
                
                if (is_array($value)) {
                    $value = $this->inflate($value);
                }
                
                if (method_exists($object, $setter)) {
                    $object->$setter($value);
                }
                else {
                    if ($class->hasProperty($property)) {
                        $accessor = $class->getProperty($property);
                        $accessor->setAccessible(true);
                        $accessor->setValue($object, $value);
                    }
                    else {
                        $object->$property = $value;
                    }
                }
            }
            
            return $object;
        }
        else {
            return $data;
        }
    }
}
