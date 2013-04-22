<?php
// DO NOT EDIT! Generated by Protobuf-PHP protoc plugin 0.9.4
// Source: TaskReview.proto
//   Date: 2013-04-17 16:15:03

namespace  {

  class TaskReview extends \DrSlump\Protobuf\Message {

    /**  @var int */
    public $task_id = null;
    
    /**  @var int */
    public $user_id = null;
    
    /**  @var int */
    public $corrections = null;
    
    /**  @var int */
    public $grammar = null;
    
    /**  @var int */
    public $spelling = null;
    
    /**  @var int */
    public $consistency = null;
    
    /**  @var string */
    public $comment = null;
    

    /** @var \Closure[] */
    protected static $__extensions = array();

    public static function descriptor()
    {
      $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, '.TaskReview');

      // OPTIONAL INT32 task_id = 1
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 1;
      $f->name      = "task_id";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT32 user_id = 2
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 2;
      $f->name      = "user_id";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT32 corrections = 3
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 3;
      $f->name      = "corrections";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT32 grammar = 4
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 4;
      $f->name      = "grammar";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT32 spelling = 5
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 5;
      $f->name      = "spelling";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT32 consistency = 6
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 6;
      $f->name      = "consistency";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING comment = 7
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 7;
      $f->name      = "comment";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      foreach (self::$__extensions as $cb) {
        $descriptor->addField($cb(), true);
      }

      return $descriptor;
    }

    /**
     * Check if <task_id> has a value
     *
     * @return boolean
     */
    public function hasTaskId(){
      return $this->_has(1);
    }
    
    /**
     * Clear <task_id> value
     *
     * @return \TaskReview
     */
    public function clearTaskId(){
      return $this->_clear(1);
    }
    
    /**
     * Get <task_id> value
     *
     * @return int
     */
    public function getTaskId(){
      return $this->_get(1);
    }
    
    /**
     * Set <task_id> value
     *
     * @param int $value
     * @return \TaskReview
     */
    public function setTaskId( $value){
      return $this->_set(1, $value);
    }
    
    /**
     * Check if <user_id> has a value
     *
     * @return boolean
     */
    public function hasUserId(){
      return $this->_has(2);
    }
    
    /**
     * Clear <user_id> value
     *
     * @return \TaskReview
     */
    public function clearUserId(){
      return $this->_clear(2);
    }
    
    /**
     * Get <user_id> value
     *
     * @return int
     */
    public function getUserId(){
      return $this->_get(2);
    }
    
    /**
     * Set <user_id> value
     *
     * @param int $value
     * @return \TaskReview
     */
    public function setUserId( $value){
      return $this->_set(2, $value);
    }
    
    /**
     * Check if <corrections> has a value
     *
     * @return boolean
     */
    public function hasCorrections(){
      return $this->_has(3);
    }
    
    /**
     * Clear <corrections> value
     *
     * @return \TaskReview
     */
    public function clearCorrections(){
      return $this->_clear(3);
    }
    
    /**
     * Get <corrections> value
     *
     * @return int
     */
    public function getCorrections(){
      return $this->_get(3);
    }
    
    /**
     * Set <corrections> value
     *
     * @param int $value
     * @return \TaskReview
     */
    public function setCorrections( $value){
      return $this->_set(3, $value);
    }
    
    /**
     * Check if <grammar> has a value
     *
     * @return boolean
     */
    public function hasGrammar(){
      return $this->_has(4);
    }
    
    /**
     * Clear <grammar> value
     *
     * @return \TaskReview
     */
    public function clearGrammar(){
      return $this->_clear(4);
    }
    
    /**
     * Get <grammar> value
     *
     * @return int
     */
    public function getGrammar(){
      return $this->_get(4);
    }
    
    /**
     * Set <grammar> value
     *
     * @param int $value
     * @return \TaskReview
     */
    public function setGrammar( $value){
      return $this->_set(4, $value);
    }
    
    /**
     * Check if <spelling> has a value
     *
     * @return boolean
     */
    public function hasSpelling(){
      return $this->_has(5);
    }
    
    /**
     * Clear <spelling> value
     *
     * @return \TaskReview
     */
    public function clearSpelling(){
      return $this->_clear(5);
    }
    
    /**
     * Get <spelling> value
     *
     * @return int
     */
    public function getSpelling(){
      return $this->_get(5);
    }
    
    /**
     * Set <spelling> value
     *
     * @param int $value
     * @return \TaskReview
     */
    public function setSpelling( $value){
      return $this->_set(5, $value);
    }
    
    /**
     * Check if <consistency> has a value
     *
     * @return boolean
     */
    public function hasConsistency(){
      return $this->_has(6);
    }
    
    /**
     * Clear <consistency> value
     *
     * @return \TaskReview
     */
    public function clearConsistency(){
      return $this->_clear(6);
    }
    
    /**
     * Get <consistency> value
     *
     * @return int
     */
    public function getConsistency(){
      return $this->_get(6);
    }
    
    /**
     * Set <consistency> value
     *
     * @param int $value
     * @return \TaskReview
     */
    public function setConsistency( $value){
      return $this->_set(6, $value);
    }
    
    /**
     * Check if <comment> has a value
     *
     * @return boolean
     */
    public function hasComment(){
      return $this->_has(7);
    }
    
    /**
     * Clear <comment> value
     *
     * @return \TaskReview
     */
    public function clearComment(){
      return $this->_clear(7);
    }
    
    /**
     * Get <comment> value
     *
     * @return string
     */
    public function getComment(){
      return $this->_get(7);
    }
    
    /**
     * Set <comment> value
     *
     * @param string $value
     * @return \TaskReview
     */
    public function setComment( $value){
      return $this->_set(7, $value);
    }
  }
}
