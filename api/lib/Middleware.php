<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Middleware
 *
 * @author sean
 */
require_once __DIR__."/../DataAccessObjects/AdminDao.class.php";
require_once __DIR__."/../DataAccessObjects/TaskDao.class.php";
require_once __DIR__."/../DataAccessObjects/OrganisationDao.class.php";
require_once __DIR__."/../DataAccessObjects/ProjectDao.class.php";

class Middleware
{
	
    public static function isloggedIn ($request, $response, $route)
    {
    	if(!is_null(UserDao::getLoggedInUser())) {
    		return true;
		}
		else {
			Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, "The Autherization header does not match the current user or the user does not have permission to acess the current resource");
		}

	}
	  
//        $params = $route->getParams();
//      
//        
//       
//            if(isset ($params['email'])&& isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])){
//                $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
//                $email =$params['email'];
//                 if (!is_numeric($email) && strstr($email, '.')) {
//                    $temp = array();
//                    $temp = explode('.', $email);
//                    $lastIndex = sizeof($temp)-1;
//                    if ($lastIndex > 1) {
//                        $format='.'.$temp[$lastIndex];
//                        $email = $temp[0];
//                        for ($i = 1; $i < $lastIndex; $i++) {
//                            $email = "{$email}.{$temp[$i]}";
//                        }
//                    }
//                }
//                $openidHash = md5($email.substr(Settings::get("session.site_key"),0,20));
//                if ($headerHash!=$openidHash) {
//                    Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, "The Autherization header does not match the current user or the user does not have permission to acess the current resource");
//                } 
//            }
        
//    } 
    
    
    public static function Registervalidation ($request, $response, $route) 
    {
        $params = $route->getParams();
        if (isset($params['email']) && isset($_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'])) {
            $headerHash = $_SERVER['HTTP_X_CUSTOM_AUTHORIZATION'];
            $email =$params['email'];
            if (!is_numeric($email) && strstr($email, '.')) {
                $temp = array();
                $temp = explode('.', $email);
                $lastIndex = sizeof($temp)-1;
                if ($lastIndex > 1) {
                    $format='.'.$temp[$lastIndex];
                    $email = $temp[0];
                    for ($i = 1; $i < $lastIndex; $i++) {
                        $email = "{$email}.{$temp[$i]}";
                    }
                }
            }
            $openidHash = md5($email.substr(Settings::get("session.site_key"),0,20));
            if ($headerHash!=$openidHash) {
                Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, "The user does not have permission to acess the current resource");
            } 
        } else {
            self::authUserOwnsResource ($request, $response, $route);
        }
    }
	
	// Does the user Id match the Id of the resources owner
	public static function authUserOwnsResource($request, $response, $route)
    {
    	if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
	        $params = $route->getParams();
	        $userId=$params['userId'];
	        if (!is_numeric($userId) && strstr($userId, '.')) {
	                $userId = explode('.', $userId);
	                $format = '.'.$userId[1];
	                $userId = $userId[0];
	        }
	        if ($userId!=$user->getId()) {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
        }
    } 
    
    public static function notFound()
    {
        Dispatcher::getDispatcher()->redirect(Dispatcher::getDispatcher()->urlFor('getLoginTemplate'));
    }
    
    private static function isSiteAdmin($userId)
    {
        
        return AdminDao::isAdmin($userId,null);
    }
	
	
	private static function getOrgIdFromTaskId($taskId)
	{
		$projectId = null;
		if($taskId != null) {
			$tasks = TaskDao::getTask($taskId);
			$task = $tasks[0];
			$projectId = $task->getProjectId();
		}
		return self::getOrgIdFromProjectId($projectId);	
	}
	
	private static function getOrgIdFromProjectId($projectId)
	{		
		$orgId = null;
		if ($projectId != null) {
			$projects = ProjectDao::getProject($projectId);
			$project = $projects[0];
			$orgId = $project->getOrganisationId();
		}
		return $orgId;
	}
	
	private static function getOrgIdFromBadgeId($badgeId)
	{
		$orgId = null;
		if ($badgeId != null) {
			$badges = BadgeDao::getBadge($badgeId);
			$badge = $badges[0];
			$orgId = $badge->getOwnerId();
		}
		return $orgId;
	}
	
	// Is the user a site admin
	public static function authenticateSiteAdmin($request, $response, $route)
    {
    	if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
		    if (self::isSiteAdmin($user->getId())) {
		    	return true;
		    }
			else {
				 Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
		                "The user does not have permission to acess the current resource");
			}
		}
    }
	
	// Is the user a member of ANY Organisation 
	public static function authenticateUserMembership($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
			$userId = $user->getId();
			$userOrgList = UserDao::findOrganisationsUserBelongsTo($user_id);
			if($userOrgList != null && count($userOrgList) > 0) {
				return true;				
			}
			else {
				 Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
        				"The user does not have permission to acess the current resource");
			}
		}
	}
	
	// Is the user an Admin of the Organisation releated to the request
	public static function authenticateOrgAdmin($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
			$userId = $user->getId();
	        $params = $route->getParams();
			$orgId = null;
			if ($params != null) {
				$orgId = $params['orgId'];
				if (!is_numeric($orgId)&& strstr($orgId, '.')) {
	                $orgId = explode('.', $orgId);
	                $format = '.'.$orgId[1];
	                $orgId = $orgId[0];
	            }			
			}
			if ($orgId != null && AdminDao::isAdmin($userId, $orgId)) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}
	}
	
	// Is the user a member of the Organisation related to the request
	public static function authenticateOrgMember($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
	        $userId = $user->getId();
	        $params = $route->getParams();
			$orgId = null;
			if ($params != null) {
				$orgId = $params['orgId'];
				if (!is_numeric($orgId)&& strstr($orgId, '.')) {
	                $orgId = explode('.', $orgId);
	                $format = '.'.$orgId[1];
	                $orgId = $orgId[0];
	            }			
			}
			if ($orgId != null && OrganisationDao::isMember($orgId, $userId)) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}       
	}
	
	// Is the user a member of the Organisation who created the Project in question
	public static function authenticateUserForOrgProject($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
	        $userId = $user->getId();
			$params = $route->getParams();
			
			$projectId = null;
			if ($params != null) {
				$projectId = $params['projectId'];
				if (!is_numeric($projectId)&& strstr($projectId, '.')) {
	                $projectId = explode('.', $projectId);
	                $format = '.'.$projectId[1];
	                $projectId = $projectId[0];
	            }			
			}
			
			$orgId = self::getOrgIdFromProjectId($projectId);
			if ($orgId != null && OrganisationDao::isMember($orgId, $userId)) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}	
	}
	
	//Is the user a member of the Organisation who created the Task in question
	public static function authenticateUserForOrgTask($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
	        $userId = $user->getId();
			$params = $route->getParams();
			
			$taskId = null;
			if ($params != null) {
				$taskId = $params['taskId'];
				if (!is_numeric($taskId)&& strstr($taskId, '.')) {
	                $taskId = explode('.', $taskId);
	                $format = '.'.$taskId[1];
	                $taskId = $taskId[0];
	            }			
			}			
			
			$orgId = self::getOrgIdFromTaskId($taskId);
			if ($orgId != null && OrganisationDao::isMember($orgId, $userId)) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}
		
	}

	// Does the user id match the current user or does the current user belong to the organisation that created the task in question
	public static function authUserOrOrgForTask($request, $response, $route)
	{			
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
	        $params = $route->getParams();
			//in this function userId refers to the id being tested which may not be the currently logged in user
	        $userId = $params['userId'];
	        if (!is_numeric($userId) && strstr($userId, '.')) {
	                $userId = explode('.', $userId);
	                $format = '.'.$userId[1];
	                $userId = $userId[0];
	        }
			$taskId = $params['taskId'];
			if (!is_numeric($taskId) && strstr($taskId, '.')) {
	                $taskId = explode('.', $taskId);
	                $format = '.'.$taskId[1];
	                $taskId = $taskId[0];
	        }
			$orgId = self::getOrgIdFromTaskId($taskId);
			
			if($userId == $user->getId()) {
				return true;
			}
			else if($orgId != null && OrganisationDao::isMember($orgId, $userId)) {
				return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
        }
	}

	//Is the current user a member of the Organisation who created the Badge in question
	public static function authenticateUserForOrgBadge($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
			$userId = $user->getId();
	        $params = $route->getParams();
			
			$badgeId = null;
			if ($params != null) {
				$badgeId = $params['badgeId'];
				if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
	                $badgeId = explode('.', $badgeId);
	                $format = '.'.$badgeId[1];
	                $badgeId = $badgeId[0];
	            }			
			}			
			
			$orgId = self::getOrgIdFromBadgeId($badgeId);
					
			// currently this checks if the orgId is not Null
			// cases where the orgId is null signify a system badge
			// using this middleware function will lead to errors unless those are accounted for	
			if($orgId != null && OrganisationDao::isMember($orgId, $userId)) {
				return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
		}		
	}

	//Does the user id match the current user or is the current user a member of the Organisation who created the Badge in question
	public static function authenticateUserOrOrgForOrgBadge($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
	        $params = $route->getParams();
			//in this function userId refers to the id being tested which may not be the currently logged in user
	        $userId = $params['userId'];
	        if (!is_numeric($userId) && strstr($userId, '.')) {
	                $userId = explode('.', $userId);
	                $format = '.'.$userId[1];
	                $userId = $userId[0];
	        }
			
			$badgeId = null;
			if ($params != null) {
				$badgeId = $params['badgeId'];
				if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
	                $badgeId = explode('.', $badgeId);
	                $format = '.'.$badgeId[1];
	                $badgeId = $badgeId[0];
	            }			
			}			
			
			$orgId = self::getOrgIdFromBadgeId($badgeId);		
			if($userId == $user->getId()) {
				return true;
			}
			// currently this checks if the orgId is not Null
			// cases where the orgId is null signify a system badge
			// using this middleware function will lead to errors unless those are accounted for	
			else if($orgId != null && OrganisationDao::isMember($orgId, $userId)) {
				return true;
			}
			else {
	            Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
	        }
		}		
	}

	
	// Does User have required badge
	public static function authenticateUserHasBadge($request, $response, $route)
	{
		if(self::isloggedIn($request, $response, $route))
		{
	        $user = UserDao::getLoggedInUser();
	        if (self::isSiteAdmin($user->getId())) {
	        	return true;
	        }
			$userId = $user->getId();
			$params = $route->getParams();
			
			$badgeId = null;
			if ($params != null) {
				$badgeId = $params['badgeId'];
				if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
	                $badgeId = explode('.', $badgeId);
	                $format = '.'.$badgeId[1];
	                $badgeId = $badgeId[0];
	            }			
			}
			if ($badgeId != null && BadgeDao::validateUserBadge($userId, $badgeId)) {
				return true;
			}
			else {
				Dispatcher::getDispatcher()->halt(HttpStatusEnum::FORBIDDEN, 
	                    "The user does not have permission to acess the current resource");
			}
		}
	}
	


	
//    public static function authenticateUserForTask($request, $response, $route) 
//    {
//        if (self::isSiteAdmin()) {
//            return true;
//        }
//
//        $params = $route->getParams(); 
//
//        self::authUserIsLoggedIn();
//        $user_id = UserSession::getCurrentUserID();
//        $claimant = null;
//        if ($params !== null) {
//            $task_id = $params['task_id'];
//            $claimant =TaskDao::getUserClaimedTask($task_id);             
//        }
//        return !$claimant || $user_id == $claimant->getId();
//    }

//    public static function authUserForOrg($request, $response, $route) 
//    {
//        if (self::isSiteAdmin()) {
//            return true;
//        }
//        $user_id = UserSession::getCurrentUserID();
//        $params = $route->getParams();
//       if ($params !== null) {
//            $org_id = $params['org_id'];
//            if ($user_id) {
//                $user_orgs =OrganisationDao::getOrgByUser($user_id);
//                if (!is_null($user_orgs)) {
//                    foreach ($user_orgs as $orgObject) {
//                        if ($orgObject->getId() == $org_id) {
//                            return true;
//                       }
//                    }
//                }
//            }
//        }
//        
//       self::notFound();
//    }

    /*
     *  Middleware for ensuring the current user belongs to the Org that uploaded the associated Task
     *  Used for altering task details
     */
//    public static function authUserForOrgTask($request, $response, $route) 
//    {
//        if (self::isSiteAdmin()) {
//            return true;
//        }
//
//        
//        $params= $route->getParams();
//        if ($params != null) {
//            $task_id = $params['task_id'];
//            $task = TaskDao::getTask($task_id);
//            $task = is_array($task)?$task[0]:$task;
//            $project =ProjectDao::getProject($task->getProjectId());
//            $project = is_array($project)?$project[0]:$project;
//            $org_id = $project->getOrganisationId();
//            $user_id = UserSession::getCurrentUserID();
//
//            if ($user_id && OrganisationDao::isMember($org_id, $user_id)) {
//                return true;
//            }
//        }
//       
//        self::notFound();
//    } 
//    
//    public static function authUserForOrgProject($request, $response, $route) 
//    {                        
//        if ($this->isSiteAdmin()) {
//            return true;
//        }
//
//        $params = $route->getParams();
//        $userDao = new UserDao();
//        $projectDao = new ProjectDao();
//        
//        if ($params != null) {
//            $user_id = UserSession::getCurrentUserID();
//            $project_id = $params['project_id'];   
//            $userOrgs = $userDao->getUserOrgs($user_id);
//            $project = $projectDao->getProject($project_id); 
//            $project_orgid = $project->getOrganisationId();
//
//            if($userOrgs) {
//                foreach($userOrgs as $org)
//                {                
//                    if($org->getId() == $project_orgid) {
//                        return true;
//                    }
//                }
//            }
//        }
//        self::notFound();
//    }    
//
//    public static function authUserForTaskDownload($request, $response, $route)
//    {
//        if ($this->isSiteAdmin()) {
//            return true;
//        }
//
//        $taskDao = new TaskDao();
//        $projectDao = new ProjectDao();
//        $userDao = new UserDao();
//
//        $params= $route->getParams();
//        if ($params != null) {
//            $task_id = $params['task_id'];
//            $task = $taskDao->getTask($task_id);
////            if($taskDao->getUserClaimedTask($task_id) && $task->getStatus() != TaskStatusEnum::COMPLETE) return true;
//            if($taskDao->getUserClaimedTask($task_id)) return true;
//
//            $project = $projectDao->getProject($task->getProjectId());
//            
//            $org_id = $project->getOrganisationId();
//            $user_id = UserSession::getCurrentUserID();
//
//            if ($user_id) {
//                $user_orgs = $userDao->getUserOrgs($user_id);
//                if (!is_null($user_orgs)) {
//                    foreach ($user_orgs as $orgObject) {
//                        if ($orgObject->getId() == $org_id) {
//                            return true;
//                        }
//                    }
//                }                
//            }
//        }
//       
//        self::notFound();
//    }
    
   
    
    
    
}

?>
