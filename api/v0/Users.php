<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author sean
 */
require_once '../app/UserDao.class.php';
require_once '../app/TaskDao.class.php';
require_once '../app/lib/Notify.class.php';
require_once '../app/lib/NotificationTypes.class.php';
class Users {
   

    public static function init(){
        $dispatcher=Dispatcher::getDispatcher();
      
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users(:format)/', function ($format=".json"){
           Dispatcher::sendResponce(null, "display all users", null, $format);
        },'getUsers');
        
         Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/', function ($id,$format=".json"){
           if(!is_numeric($id)&& strstr($id, '.')){
               $id= explode('.', $id);
               $format='.'.$id[1];
               $id=$id[0];
           }
           $dao = new UserDao();
           $data= $dao->find(array("user_id"=>$id));
           if(is_array($data))$data=$data[0];
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getUser');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/getByEmail/:email/', function ($email,$format=".json"){
           if(!is_numeric($email)&& strstr($email, '.')){
               $temp = array();
               $temp= explode('.', $email);
               $lastIndex = sizeof($temp)-1;
               if($lastIndex>1){
                   $format='.'.$temp[$lastIndex];
                   $email=$temp[0];
                   for($i = 1; $i < $lastIndex; $i++){
                       $email="{$email}.{$temp[$i]}";
                   }
               }
           }
           $dao = new UserDao();
           $data= $dao->getUser(null, $email, null, null, null, null, null,null,null);
           if(is_array($data))$data=$data[0];
           Dispatcher::sendResponce(null, $data, null, $format);
        },'getUserByEmail');
       
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/orgs(:format)/', function ($id,$format=".json"){
          $dao = new UserDao();
          Dispatcher::sendResponce(null, $dao->findOrganisationsUserBelongsTo($id), null, $format);
        },'getUserOrgs');
       
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/badges(:format)/', function ($id,$format=".json"){
          $dao = new UserDao();
          Dispatcher::sendResponce(null, $dao->getUserBadgesbyID($id), null, $format);
        },'getUserbadges');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/tags(:format)/', function ($id,$format=".json"){
           $dao = new UserDao();
           Dispatcher::sendResponce(null, $dao->getUserTags($id), null, $format);
        },'getUsertags');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/tasks(:format)/', function ($id,$format=".json"){
           $dao = new TaskDao();
           Dispatcher::sendResponce(null, $dao->getUserTasksByID($id), null, $format);
        },'getUsertasks');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/users/:id/tasks(:format)/', function ($id,$format=".json"){
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $data= APIHelper::cast("Task", $data);
            $dao = new TaskDao;
            Dispatcher::sendResponce(null, array("result"=>$dao->claimTaskbyID($data->getTaskId(), $id)), null, $format);
            $dao = new UserDao();
            Notify::notifyUserClaimedTask($dao->find(array("user_id"=>$id)), $data);
            Notify::sendEmailNotifications($data, NotificationTypes::Claim);
        },'userClaimTask');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/top_tasks(:format)/', function ($id,$format=".json"){
            $limit=5;
            if(isset ($_GET['limit'])&& is_numeric($_GET['limit'])) $limit= $_GET['limit'];
            $dao = new TaskDao();
            $data=$dao->getUserTopTasks($id,$limit);
            Dispatcher::sendResponce(null, $data , null, $format);
        },'getUserTopTasks');
        
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/users/:id/', function ($id,$format=".json"){
            if(!is_numeric($id)&& strstr($id, '.')){
                $id= explode('.', $id);
                $format='.'.$id[1];
                $id=$id[0];
            }
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $data= APIHelper::cast(new \User(array()), $data);
            $dao = new UserDao();
            $data->setUserId($id);
            $data = $dao->save($data);
            $data= APIHelper::cast(new \User(array()), $data);
            if(is_array($data))$data=$data[0];
            Dispatcher::sendResponce(null, $data, null, $format);
        },'updateUser');
        
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/users/:id/tags(:format)/', function ($id,$format=".json"){
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $data= APIHelper::cast(new Tag(array()), $data);
            $dao = new UserDao();
            $data = $dao->likeTag($id,$data->getTagId());
            if(is_array($data))$data=$data[0];
            Dispatcher::sendResponce(null, $data, null, $format);
        },'addUsertag');
        
        Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/users/:id/tags/:tagId/', function ($id,$tagId,$format=".json"){
            if(!is_numeric($tagId)&& strstr($tagId, '.')){
                $tagId= explode('.', $tagId);
                $format='.'.$tagId[1];
                $tagId=$tagId[0];
            }
            $dao = new UserDao();
            $data = $dao->likeTag($id,$tagId);
            if(is_array($data))$data=$data[0];
            Dispatcher::sendResponce(null, $data, null, $format);
        },'addUserTagById');
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/users/:id/tags/:tagId/', function ($id,$tagId,$format=".json"){
            if(!is_numeric($tagId)&& strstr($tagId, '.')){
                $tagId= explode('.', $tagId);
                $format='.'.$tagId[1];
                $tagId=$tagId[0];
            }
            $dao = new UserDao();
            $data = $dao->removeTag($id,$tagId);
            if(is_array($data))$data=$data[0];
            Dispatcher::sendResponce(null, $data, null, $format);
        },'deleteUserTagById');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/tracked_tasks(:format)/', function ($id,$format=".json"){
            $dao = new UserDao();
            $data=$dao->getTrackedTasks($id);
            Dispatcher::sendResponce(null, $data , null, $format);
        },'getUserTrackedTasks');
        Dispatcher::registerNamed(HttpMethodEnum::POST, '/v0/users/:id/tracked_tasks(:format)/', function ($id,$format=".json"){
            $dao = new UserDao();
            $data=Dispatcher::getDispatcher()->request()->getBody();
            $data= APIHelper::deserialiser($data, $format);
            $data= APIHelper::cast("Task", $data);
            $data=$dao->trackTask($id, $data->getTaskId());
            Dispatcher::sendResponce(null, $data , null, $format);
        },'addUserTrackedTasks');
         Dispatcher::registerNamed(HttpMethodEnum::PUT, '/v0/users/:id/tracked_tasks/:taskID/', function ($id,$taskID,$format=".json"){
            if(!is_numeric($taskID)&& strstr($taskID, '.')){
                $taskID= explode('.', $taskID);
                $format='.'.$taskID[1];
                $taskID=$taskID[0];
            }
            $dao = new UserDao();
            $data=$dao->trackTask($id, $taskID);
            Dispatcher::sendResponce(null, $data , null, $format);
        },'addUserTrackedTasksById');
        Dispatcher::registerNamed(HttpMethodEnum::DELETE, '/v0/users/:id/tracked_tasks/:taskID/', function ($id,$taskID,$format=".json"){
            if(!is_numeric($taskID)&& strstr($taskID, '.')){
                $taskID= explode('.', $taskID);
                $format='.'.$taskID[1];
                $taskID=$taskID[0];
            }
            $dao = new UserDao();
            $data=$dao->ignoreTask($id,$taskID);
            Dispatcher::sendResponce(null, $data , null, $format);
        },'deleteUserTrackedTasksById');
        
        Dispatcher::registerNamed(HttpMethodEnum::GET, '/v0/users/:id/has_reset_request(:format)/', function ($id,$format=".json"){
            $dao = new UserDao();
            $data=$dao->hasRequestedPasswordResetID($id);
            Dispatcher::sendResponce(null, $data , null, $format);
        },'hasUserRequestedPasswordResetID');
        
    }
    
   
    
}
Users::init();
?>