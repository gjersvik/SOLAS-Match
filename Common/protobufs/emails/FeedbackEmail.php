<?php
// DO NOT EDIT! Generated by Protobuf-PHP protoc plugin 0.9.4
// Source: FeedbackEmail.proto
//   Date: 2013-01-28 16:13:49

namespace  {

  class FeedbackEmail extends \DrSlump\Protobuf\Message {

    /**  @var int - \EmailMessage\Type */
    public $email_type = \EmailMessage\Type::FeedbackEmail;
    
    /**  @var int */
    public $taskId = null;
    
    /**  @var int[]  */
    public $userId = array();
    
    /**  @var string */
    public $feedback = null;
    

    /** @var \Closure[] */
    protected static $__extensions = array();

    public static function descriptor()
    {
      $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, '.FeedbackEmail');

      // REQUIRED ENUM email_type = 1
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 1;
      $f->name      = "email_type";
      $f->type      = \DrSlump\Protobuf::TYPE_ENUM;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
      $f->reference = '\EmailMessage\Type';
      $f->default   = \EmailMessage\Type::FeedbackEmail;
      $descriptor->addField($f);

      // REQUIRED INT32 taskId = 2
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 2;
      $f->name      = "taskId";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
      $descriptor->addField($f);

      // REPEATED INT32 userId = 3
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 3;
      $f->name      = "userId";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_REPEATED;
      $descriptor->addField($f);

      // REQUIRED STRING feedback = 4
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 4;
      $f->name      = "feedback";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
      $descriptor->addField($f);

      foreach (self::$__extensions as $cb) {
        $descriptor->addField($cb(), true);
      }

      return $descriptor;
    }

    /**
     * Check if <email_type> has a value
     *
     * @return boolean
     */
    public function hasEmailType(){
      return $this->_has(1);
    }
    
    /**
     * Clear <email_type> value
     *
     * @return \FeedbackEmail
     */
    public function clearEmailType(){
      return $this->_clear(1);
    }
    
    /**
     * Get <email_type> value
     *
     * @return int - \EmailMessage\Type
     */
    public function getEmailType(){
      return $this->_get(1);
    }
    
    /**
     * Set <email_type> value
     *
     * @param int - \EmailMessage\Type $value
     * @return \FeedbackEmail
     */
    public function setEmailType( $value){
      return $this->_set(1, $value);
    }
    
    /**
     * Check if <taskId> has a value
     *
     * @return boolean
     */
    public function hasTaskId(){
      return $this->_has(2);
    }
    
    /**
     * Clear <taskId> value
     *
     * @return \FeedbackEmail
     */
    public function clearTaskId(){
      return $this->_clear(2);
    }
    
    /**
     * Get <taskId> value
     *
     * @return int
     */
    public function getTaskId(){
      return $this->_get(2);
    }
    
    /**
     * Set <taskId> value
     *
     * @param int $value
     * @return \FeedbackEmail
     */
    public function setTaskId( $value){
      return $this->_set(2, $value);
    }
    
    /**
     * Check if <userId> has a value
     *
     * @return boolean
     */
    public function hasUserId(){
      return $this->_has(3);
    }
    
    /**
     * Clear <userId> value
     *
     * @return \FeedbackEmail
     */
    public function clearUserId(){
      return $this->_clear(3);
    }
    
    /**
     * Get <userId> value
     *
     * @param int $idx
     * @return int
     */
    public function getUserId($idx = NULL){
      return $this->_get(3, $idx);
    }
    
    /**
     * Set <userId> value
     *
     * @param int $value
     * @return \FeedbackEmail
     */
    public function setUserId( $value, $idx = NULL){
      return $this->_set(3, $value, $idx);
    }
    
    /**
     * Get all elements of <userId>
     *
     * @return int[]
     */
    public function getUserIdList(){
     return $this->_get(3);
    }
    
    /**
     * Add a new element to <userId>
     *
     * @param int $value
     * @return \FeedbackEmail
     */
    public function addUserId( $value){
     return $this->_add(3, $value);
    }
    
    /**
     * Check if <feedback> has a value
     *
     * @return boolean
     */
    public function hasFeedback(){
      return $this->_has(4);
    }
    
    /**
     * Clear <feedback> value
     *
     * @return \FeedbackEmail
     */
    public function clearFeedback(){
      return $this->_clear(4);
    }
    
    /**
     * Get <feedback> value
     *
     * @return string
     */
    public function getFeedback(){
      return $this->_get(4);
    }
    
    /**
     * Set <feedback> value
     *
     * @param string $value
     * @return \FeedbackEmail
     */
    public function setFeedback( $value){
      return $this->_set(4, $value);
    }
  }
}
