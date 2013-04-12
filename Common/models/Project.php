<?php
// DO NOT EDIT! Generated by Protobuf-PHP protoc plugin 0.9.4
// Source: Project.proto
//   Date: 2013-04-12 13:42:01

namespace  {

  class Project extends \DrSlump\Protobuf\Message {

    /**  @var int */
    public $id = null;
    
    /**  @var string */
    public $title = null;
    
    /**  @var string */
    public $description = null;
    
    /**  @var string */
    public $deadline = null;
    
    /**  @var int */
    public $organisationId = null;
    
    /**  @var string */
    public $impact = null;
    
    /**  @var string */
    public $reference = null;
    
    /**  @var int */
    public $wordCount = null;
    
    /**  @var string */
    public $createdTime = null;
    
    /**  @var string */
    public $status = null;
    
    /**  @var \Locale */
    public $sourceLocale = null;
    
    /**  @var \Tag[]  */
    public $tag = array();
    

    /** @var \Closure[] */
    protected static $__extensions = array();

    public static function descriptor()
    {
      $descriptor = new \DrSlump\Protobuf\Descriptor(__CLASS__, '.Project');

      // OPTIONAL INT32 id = 1
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 1;
      $f->name      = "id";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING title = 2
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 2;
      $f->name      = "title";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING description = 3
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 3;
      $f->name      = "description";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING deadline = 4
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 4;
      $f->name      = "deadline";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT32 organisationId = 5
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 5;
      $f->name      = "organisationId";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING impact = 6
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 6;
      $f->name      = "impact";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING reference = 7
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 7;
      $f->name      = "reference";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL INT32 wordCount = 8
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 8;
      $f->name      = "wordCount";
      $f->type      = \DrSlump\Protobuf::TYPE_INT32;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING createdTime = 9
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 9;
      $f->name      = "createdTime";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL STRING status = 10
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 10;
      $f->name      = "status";
      $f->type      = \DrSlump\Protobuf::TYPE_STRING;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $descriptor->addField($f);

      // OPTIONAL MESSAGE sourceLocale = 11
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 11;
      $f->name      = "sourceLocale";
      $f->type      = \DrSlump\Protobuf::TYPE_MESSAGE;
      $f->rule      = \DrSlump\Protobuf::RULE_OPTIONAL;
      $f->reference = '\Locale';
      $descriptor->addField($f);

      // REPEATED MESSAGE tag = 12
      $f = new \DrSlump\Protobuf\Field();
      $f->number    = 12;
      $f->name      = "tag";
      $f->type      = \DrSlump\Protobuf::TYPE_MESSAGE;
      $f->rule      = \DrSlump\Protobuf::RULE_REPEATED;
      $f->reference = '\Tag';
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
     * @return \Project
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
     * @return \Project
     */
    public function setId( $value){
      return $this->_set(1, $value);
    }
    
    /**
     * Check if <title> has a value
     *
     * @return boolean
     */
    public function hasTitle(){
      return $this->_has(2);
    }
    
    /**
     * Clear <title> value
     *
     * @return \Project
     */
    public function clearTitle(){
      return $this->_clear(2);
    }
    
    /**
     * Get <title> value
     *
     * @return string
     */
    public function getTitle(){
      return $this->_get(2);
    }
    
    /**
     * Set <title> value
     *
     * @param string $value
     * @return \Project
     */
    public function setTitle( $value){
      return $this->_set(2, $value);
    }
    
    /**
     * Check if <description> has a value
     *
     * @return boolean
     */
    public function hasDescription(){
      return $this->_has(3);
    }
    
    /**
     * Clear <description> value
     *
     * @return \Project
     */
    public function clearDescription(){
      return $this->_clear(3);
    }
    
    /**
     * Get <description> value
     *
     * @return string
     */
    public function getDescription(){
      return $this->_get(3);
    }
    
    /**
     * Set <description> value
     *
     * @param string $value
     * @return \Project
     */
    public function setDescription( $value){
      return $this->_set(3, $value);
    }
    
    /**
     * Check if <deadline> has a value
     *
     * @return boolean
     */
    public function hasDeadline(){
      return $this->_has(4);
    }
    
    /**
     * Clear <deadline> value
     *
     * @return \Project
     */
    public function clearDeadline(){
      return $this->_clear(4);
    }
    
    /**
     * Get <deadline> value
     *
     * @return string
     */
    public function getDeadline(){
      return $this->_get(4);
    }
    
    /**
     * Set <deadline> value
     *
     * @param string $value
     * @return \Project
     */
    public function setDeadline( $value){
      return $this->_set(4, $value);
    }
    
    /**
     * Check if <organisationId> has a value
     *
     * @return boolean
     */
    public function hasOrganisationId(){
      return $this->_has(5);
    }
    
    /**
     * Clear <organisationId> value
     *
     * @return \Project
     */
    public function clearOrganisationId(){
      return $this->_clear(5);
    }
    
    /**
     * Get <organisationId> value
     *
     * @return int
     */
    public function getOrganisationId(){
      return $this->_get(5);
    }
    
    /**
     * Set <organisationId> value
     *
     * @param int $value
     * @return \Project
     */
    public function setOrganisationId( $value){
      return $this->_set(5, $value);
    }
    
    /**
     * Check if <impact> has a value
     *
     * @return boolean
     */
    public function hasImpact(){
      return $this->_has(6);
    }
    
    /**
     * Clear <impact> value
     *
     * @return \Project
     */
    public function clearImpact(){
      return $this->_clear(6);
    }
    
    /**
     * Get <impact> value
     *
     * @return string
     */
    public function getImpact(){
      return $this->_get(6);
    }
    
    /**
     * Set <impact> value
     *
     * @param string $value
     * @return \Project
     */
    public function setImpact( $value){
      return $this->_set(6, $value);
    }
    
    /**
     * Check if <reference> has a value
     *
     * @return boolean
     */
    public function hasReference(){
      return $this->_has(7);
    }
    
    /**
     * Clear <reference> value
     *
     * @return \Project
     */
    public function clearReference(){
      return $this->_clear(7);
    }
    
    /**
     * Get <reference> value
     *
     * @return string
     */
    public function getReference(){
      return $this->_get(7);
    }
    
    /**
     * Set <reference> value
     *
     * @param string $value
     * @return \Project
     */
    public function setReference( $value){
      return $this->_set(7, $value);
    }
    
    /**
     * Check if <wordCount> has a value
     *
     * @return boolean
     */
    public function hasWordCount(){
      return $this->_has(8);
    }
    
    /**
     * Clear <wordCount> value
     *
     * @return \Project
     */
    public function clearWordCount(){
      return $this->_clear(8);
    }
    
    /**
     * Get <wordCount> value
     *
     * @return int
     */
    public function getWordCount(){
      return $this->_get(8);
    }
    
    /**
     * Set <wordCount> value
     *
     * @param int $value
     * @return \Project
     */
    public function setWordCount( $value){
      return $this->_set(8, $value);
    }
    
    /**
     * Check if <createdTime> has a value
     *
     * @return boolean
     */
    public function hasCreatedTime(){
      return $this->_has(9);
    }
    
    /**
     * Clear <createdTime> value
     *
     * @return \Project
     */
    public function clearCreatedTime(){
      return $this->_clear(9);
    }
    
    /**
     * Get <createdTime> value
     *
     * @return string
     */
    public function getCreatedTime(){
      return $this->_get(9);
    }
    
    /**
     * Set <createdTime> value
     *
     * @param string $value
     * @return \Project
     */
    public function setCreatedTime( $value){
      return $this->_set(9, $value);
    }
    
    /**
     * Check if <status> has a value
     *
     * @return boolean
     */
    public function hasStatus(){
      return $this->_has(10);
    }
    
    /**
     * Clear <status> value
     *
     * @return \Project
     */
    public function clearStatus(){
      return $this->_clear(10);
    }
    
    /**
     * Get <status> value
     *
     * @return string
     */
    public function getStatus(){
      return $this->_get(10);
    }
    
    /**
     * Set <status> value
     *
     * @param string $value
     * @return \Project
     */
    public function setStatus( $value){
      return $this->_set(10, $value);
    }
    
    /**
     * Check if <sourceLocale> has a value
     *
     * @return boolean
     */
    public function hasSourceLocale(){
      return $this->_has(11);
    }
    
    /**
     * Clear <sourceLocale> value
     *
     * @return \Project
     */
    public function clearSourceLocale(){
      return $this->_clear(11);
    }
    
    /**
     * Get <sourceLocale> value
     *
     * @return \Locale
     */
    public function getSourceLocale(){
      return $this->_get(11);
    }
    
    /**
     * Set <sourceLocale> value
     *
     * @param \Locale $value
     * @return \Project
     */
    public function setSourceLocale(\Locale $value){
      return $this->_set(11, $value);
    }
    
    /**
     * Check if <tag> has a value
     *
     * @return boolean
     */
    public function hasTag(){
      return $this->_has(12);
    }
    
    /**
     * Clear <tag> value
     *
     * @return \Project
     */
    public function clearTag(){
      return $this->_clear(12);
    }
    
    /**
     * Get <tag> value
     *
     * @param int $idx
     * @return \Tag
     */
    public function getTag($idx = NULL){
      return $this->_get(12, $idx);
    }
    
    /**
     * Set <tag> value
     *
     * @param \Tag $value
     * @return \Project
     */
    public function setTag(\Tag $value, $idx = NULL){
      return $this->_set(12, $value, $idx);
    }
    
    /**
     * Get all elements of <tag>
     *
     * @return \Tag[]
     */
    public function getTagList(){
     return $this->_get(12);
    }
    
    /**
     * Add a new element to <tag>
     *
     * @param \Tag $value
     * @return \Project
     */
    public function addTag(\Tag $value){
     return $this->_add(12, $value);
    }
  }
}

