<?php
namespace Aspyct\MongoMap;

abstract class Manipulator {
    const CLASS_TAG = '__php_class';
    
    private $classTag;
    
    public function __construct() {
        $this->classTag = self::CLASS_TAG;
    }
    
    public function getClassTag() {
        return $this->classTag ?: self::CLASS_TAG;
    }

    public function setClassTag($classTag) {
        $this->classTag = $classTag;
    }
}
