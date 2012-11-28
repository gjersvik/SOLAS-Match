<?php

require_once __DIR__."/../models/MembershipRequest.php";
require_once __DIR__."/../models/ArchivedTask.php";

class ModelFactory
{
    public static function BuildModel($modelName, $modelData)
    {
        $ret = null;

        switch($modelName)
        {
            case "MembershipRequest":
                $ret = ModelFactory::GenerateMembershipRequest($modelData);
                break;
            case "ArchivedTask":
                $ret = ModelFactory::GenerateArchivedTask($modelData);
                break;
            case "PasswordReset":
                $ret = ModelFactory::GeneratePasswordReset($modelData);
                break;
            default:
                echo "Unable to build model $modelName";
        }

        return $ret;
    }

    private static function GenerateMembershipRequest($modelData)
    {
        $ret = new MembershipRequest();
        $ret ->setId($modelData["request_id"]);
        if(isset($modelData['user_id'])) {
            $ret->setUserId($modelData['user_id']);
        }
        if(isset($modelData['org_id'])) {
            $ret->setOrgId($modelData['org_id']);
        }
        if(isset($modelData['request_datetime'])) {
            $ret->setRequestTime($modelData['request_datetime']);
        }
        return $ret;
    }

    private static function GenerateArchivedTask($modelData)
    {
        $ret = new ArchivedTask();

        if(isset($modelData['archive_id'])) {
            $ret->setArchiveId($modelData['archive_id']);
        }
        if(isset($modelData['task_id'])) {
            $ret->setTaskId($modelData['task_id']);
        }
        if(isset($modelData['org_id'])) {
            $ret->setOrgId($modelData['org_id']);
        }
        if(isset($modelData['title'])) {
            $ret->setTitle($modelData['title']);
        }
        if(isset($modelData['word_count'])) {
            $ret->setWordCount($modelData['word_count']);
        }
        if(isset($modelData['source_id'])) {
            $ret->setSourceId($modelData['source_id']);
        }
        if(isset($modelData['target_id'])) {
            $ret->setTargetId($modelData['target_id']);
        }
        if(isset($modelData['created_time'])) {
            $ret->setCreatedTime($modelData['created_time']);
        }
        if(isset($modelData['archived_time'])) {
            $ret->setArchivedTime($modelData['archived_time']);
        }
        if(isset($modelData['impact'])) {
            $ret->setImpact($modelData['impact']);
        }
        if(isset($modelData['reference_page'])) {
            $ret->setReferencePage($modelData['reference_page']);
        }

        return $ret;
    }

    private static function GeneratePasswordReset($modelData)
    {
        $ret = new PasswordReset();
        
        if(isset($modelData['password'])) {
            $ret->setPassword($modelData['password']);
        }
        if(isset($modelData['key'])) {
            $ret->setKey($modelData['key']);
        }

        return $ret;
    }
}
