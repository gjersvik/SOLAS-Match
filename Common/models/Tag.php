<?php
// DO NOT EDIT! Generated by Protobuf-PHP protoc plugin 0.9.4
// Source: Tag.proto
//   Date: 2013-04-16 15:58:02

namespace  {

  class Tag extends \DrSlump\Protobuf\Message {

    /**  @var int */
    public $id = null;
    
    /**  @var string */
    public $label = null;
    

    /** @var \Closure[] */
    protected static $__extensions = array();

    public static function descriptor()
    {
      $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, '.Tag');

      // OPTIONAL INT32 id = 1
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 1;
      $f->name      = "id";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // REQUIRED STRING label = 2
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 2;
      $f->name      = "label";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
      $descriptor->addField($f);

      foreach (self::$__extensions as $cb) {
        $descriptor->addField($cb(), true);
      }

      return $descriptor;
    }

    /**
     * Check if <id> has a value
     *
     * @return boolean
     */
    public function hasId(){
      return $this->_has(1);
    }
    
    /**
     * Clear <id> value
     *
     * @return \Tag
     */
    public function clearId(){
      return $this->_clear(1);
    }
    
    /**
     * Get <id> value
     *
     * @return int
     */
    public function getId(){
      return $this->_get(1);
    }
    
    /**
     * Set <id> value
     *
     * @param int $value
     * @return \Tag
     */
    public function setId( $value){
      return $this->_set(1, $value);
    }
    
    /**
     * Check if <label> has a value
     *
     * @return boolean
     */
    public function hasLabel(){
      return $this->_has(2);
    }
    
    /**
     * Clear <label> value
     *
     * @return \Tag
     */
    public function clearLabel(){
      return $this->_clear(2);
    }
    
    /**
     * Get <label> value
     *
     * @return string
     */
    public function getLabel(){
      return $this->_get(2);
    }
    
    /**
     * Set <label> value
     *
     * @param string $value
     * @return \Tag
     */
    public function setLabel( $value){
      return $this->_set(2, $value);
    }
  }
}

