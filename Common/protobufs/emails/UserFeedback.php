<?php
// DO NOT EDIT! Generated by Protobuf-PHP protoc plugin 0.9.4
// Source: UserFeedback.proto
//   Date: 2013-05-17 15:09:05

namespace  {

  class UserFeedback extends \DrSlump\Protobuf\Message {

    /**  @var int - \EmailMessage\Type */
    public $email_type = \EmailMessage\Type::UserFeedback;
    
    /**  @var int */
    public $task_id = null;
    
    /**  @var int */
    public $claimant_id = null;
    
    /**  @var string */
    public $feedback = null;
    

    /** @var \Closure[] */
    protected static $__extensions = array();

    public static function descriptor()
    {
      $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, '.UserFeedback');

      // REQUIRED ENUM email_type = 1
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 1;
      $f->name      = "email_type";
      $f->type      = \DrSlump\Protobuf::TYPE_ENUM;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
      $f->reference = '\EmailMessage\Type';
      $f->default   = \EmailMessage\Type::UserFeedback;
      $descriptor->addField($f);

      // REQUIRED INT32 task_id = 2
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 2;
      $f->name      = "task_id";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
      $descriptor->addField($f);

      // REQUIRED INT32 claimant_id = 3
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 3;
      $f->name      = "claimant_id";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_REQUIRED;
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
     * @return \UserFeedback
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
     * @return \UserFeedback
     */
    public function setEmailType( $value){
      return $this->_set(1, $value);
    }
    
    /**
     * Check if <task_id> has a value
     *
     * @return boolean
     */
    public function hasTaskId(){
      return $this->_has(2);
    }
    
    /**
     * Clear <task_id> value
     *
     * @return \UserFeedback
     */
    public function clearTaskId(){
      return $this->_clear(2);
    }
    
    /**
     * Get <task_id> value
     *
     * @return int
     */
    public function getTaskId(){
      return $this->_get(2);
    }
    
    /**
     * Set <task_id> value
     *
     * @param int $value
     * @return \UserFeedback
     */
    public function setTaskId( $value){
      return $this->_set(2, $value);
    }
    
    /**
     * Check if <claimant_id> has a value
     *
     * @return boolean
     */
    public function hasClaimantId(){
      return $this->_has(3);
    }
    
    /**
     * Clear <claimant_id> value
     *
     * @return \UserFeedback
     */
    public function clearClaimantId(){
      return $this->_clear(3);
    }
    
    /**
     * Get <claimant_id> value
     *
     * @return int
     */
    public function getClaimantId(){
      return $this->_get(3);
    }
    
    /**
     * Set <claimant_id> value
     *
     * @param int $value
     * @return \UserFeedback
     */
    public function setClaimantId( $value){
      return $this->_set(3, $value);
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
     * @return \UserFeedback
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
     * @return \UserFeedback
     */
    public function setFeedback( $value){
      return $this->_set(4, $value);
    }
  }
}
