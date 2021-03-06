<?php


require_once __DIR__."/../../Common/HttpStatusEnum.php";
require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/../../Common/models/OAuthResponce.php";
require_once __DIR__."/BaseDao.php";


class UserDao extends BaseDao
{
    
    public function __construct()
    {
        $this->client = new APIHelper(Settings::get("ui.api_format"));
        $this->siteApi = Settings::get("site.api");
    } 
    
    public function getUserDart($userId)
    {
        $ret = null;
        $helper = new APIHelper(FormatEnum::JSON);
        return $helper->serialize($this->getUser($userId));
    }
    
    public function getUser($userId)
    {
        $ret = null;
        
        $ret=CacheHelper::getCached(CacheHelper::GET_USER.$userId, TimeToLiveEnum::MINUTE,
                function($args){
                    $request = "{$args[2]}v0/users/$args[1]";
                    return $args[0]->call("User", $request);
                },
            array($this->client, $userId,$this->siteApi)); 
        return $ret;
    }
    
    public function getUserByEmail($email, $headerHash=null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/getByEmail/$email"; 
        $ret = $this->client->call("User", $request,  HttpMethodEnum::GET,null,null,null,array("X-Custom-Authorization:$headerHash"));
        return $ret;
    }

    public function isUserVerified($userId)
    {
        $ret = false;
        $request = "{$this->siteApi}v0/users/$userId/verified";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function isSubscribedToTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToTask/$userId/$taskId";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function isSubscribedToProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/subscribedToProject/$userId/$projectId";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function getUserOrgs($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/orgs";
        $ret = $this->client->call(array("Organisation"), $request);
        return $ret;
    }

    public function getUserBadges($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $ret = $this->client->call(array("Badge"), $request);
        return $ret;
    }

    public function getUserTags($userId, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }
        $ret = $this->client->call(array("Tag"), $request, HttpMethodEnum::GET, null, $args);
        return $ret;
    }

    public function getUserTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks";
        $ret = $this->client->call(array("Task"), $request);
        return $ret;
    }

    public function getUserTaskReviews($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks/$taskId/review";
        $ret = $this->client->call("TaskReview", $request);
        return $ret;
    }

    public function getUserTaskStreamNotification($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/taskStreamNotification";
        $ret = $this->client->call("UserTaskStreamNotification", $request);
        return $ret;
    }

    public function getUserTopTasks($userId, $strict = false, $limit = null, $filter = array())
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/topTasks";

        $args = array();
        if ($limit) {
            $args["limit"] = $limit;
        }

        $filterString = "";
        if ($filter) {
            if (isset($filter['taskType']) && $filter['taskType'] != '') {
                $filterString .= "taskType:".$filter['taskType'].';';
            }
            if (isset($filter['sourceLanguage']) && $filter['sourceLanguage'] != '') {
                $filterString .= "sourceLanguage:".$filter['sourceLanguage'].';';
            }
            if (isset($filter['targetLanguage']) && $filter['targetLanguage'] != '') {
                $filterString .= "targetLanguage:".$filter['targetLanguage'].';';
            }
        }

        if ($filterString != '') {
            $args['filter'] = $filterString;
        }

        $args['strict'] = $strict;

        $ret = $this->client->call(array("Task"), $request, HttpMethodEnum::GET, null, $args);
        return $ret;
    }

    public function getUserArchivedTasks($userId, $limit = null)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/archivedTasks";

        $args = null;
        if ($limit) {
            $args = array("limit" => $limit);
        }

        $ret = $this->client->call(array("ArchivedTask"), $request, HttpMethodEnum::GET, null, $args);
        return $ret;
    }

    public function getUserTrackedTasks($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/trackedTasks";
        $ret = $this->client->call(array("Task"), $request);
        return $ret;
    }

    public function getUserTrackedProjects($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects";
        $ret = $this->client->call(array("Project"), $request);
        return $ret;
    }

    public function hasUserRequestedPasswordReset($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/email/$email/passwordResetRequest";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function getPasswordResetRequestTime($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/email/$email/passwordResetRequest/time";
        $ret = $this->client->call(null, $request);
        return $ret;
    }

    public function leaveOrganisation($userId, $orgId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/leaveOrg/$userId/$orgId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function addUserBadge($userId, $badge)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges";
        $ret = $this->client->call(null, $request, HttpMethodEnum::POST, $badge);
        return $ret;
    }

    public function addUserBadgeById($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function assignBadge($email, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/assignBadge/".urlencode($email)."/$badgeId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function removeTaskStreamNotification($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/taskStreamNotification";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function requestTaskStreamNotification($notifData)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/{$notifData->getUserId()}/taskStreamNotification";
        $ret = $this->client->call(null, $request, HttpMethodEnum::PUT, $notifData);
        return $ret;
    }

    public function removeUserBadge($userId, $badgeId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/badges/$badgeId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function claimTask($userId, $task)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks";
        $ret = $this->client->call(null, $request, HttpMethodEnum::POST, $task);
        return $ret;
    }

    public function unclaimTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tasks/$taskId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function updateUser($user)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/{$user->getId()}";
        $ret = $this->client->call("User", $request, HttpMethodEnum::PUT, $user);
        CacheHelper::unCache(CacheHelper::GET_USER.$user->getId());
        return $ret;
    }

    public function addUserTag($userId, $tag)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags";
        $ret = $this->client->call(null, $request, HttpMethodEnum::POST, $tag);
        return $ret;
    }

    public function addUserTagById($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function requestReferenceEmail($userId)
    {
        $request = "{$this->siteApi}v0/users/$userId/requestReference";
        $this->client->call(null, $request, HttpMethodEnum::PUT);
    }

    public function removeUserTag($userId, $tagId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/tags/$tagId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function trackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/trackedTasks/$taskId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function untrackTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/trackedTasks/$taskId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }

    public function requestPasswordReset($email)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/email/$email/passwordResetRequest";
        $ret = $this->client->call(null, $request, HttpMethodEnum::POST);
        return $ret;
    }

    public function trackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::PUT);
        return $ret;
    }

    public function untrackProject($userId, $projectId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/projects/$projectId";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }
    
    public function openIdLogin($email,$headerhash){
    
        $ret = null;
        $request = "{$this->siteApi}v0/login/openidLogin/$email";
        $ret = $this->client->call("User", $request,  HttpMethodEnum::GET,null,null,null,array("X-Custom-Authorization:$headerhash"));
         $headers = $this->client->getHeaders();
        if(isset ($headers["X-Custom-Token"])){
            UserSession::setAccessToken($this->client->deserialize(base64_decode($headers["X-Custom-Token"]),'OAuthResponce'));
        }
        return $ret;
        
    }
    
    public function login($email, $password)
    {
        $ret = null;
        $login = new Login();
        $login->setEmail($email);
        $login->setPassword($password);
        $request = "{$this->siteApi}v0/login";
        try {        
            $ret = $this->client->call("User", $request, HttpMethodEnum::POST, $login);
        } catch(SolasMatchException $e) {
            switch($e->getCode()) {

                case HttpStatusEnum::NOT_FOUND : 
                    throw new SolasMatchException(Localisation::getTranslation(Strings::COMMON_ERROR_LOGIN_1));

                case HttpStatusEnum::UNAUTHORIZED : 
                    throw new SolasMatchException(Localisation::getTranslation(Strings::COMMON_ERROR_LOGIN_2));

                case HttpStatusEnum::FORBIDDEN :
                    $userDao = new UserDao();
                    $user = $userDao->getUserByEmail($email);

                    $adminDao = new AdminDao();                
                    $bannedUser = $adminDao->getBannedUser($user->getId());
                    throw new SolasMatchException("{$bannedUser->getComment()}");
                    
                default :
                    throw $e;
            }
        }
        
        $headers = $this->client->getHeaders();
        if(isset ($headers["X-Custom-Token"])){
            UserSession::setAccessToken($this->client->deserialize(base64_decode($headers["X-Custom-Token"]),'OAuthResponce'));
        }
        return $ret;
    }

    public function getPasswordResetRequest($key)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/passwordReset/$key";
        $ret = $this->client->call("PasswordResetRequest", $request);
        return $ret;
    }

    public function resetPassword($password, $key)
    {
        $ret = null;
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($password);
        $passwordReset->setKey($key);
        $request = "{$this->siteApi}v0/passwordReset";
        $ret = $this->client->call(null, $request, HttpMethodEnum::POST, $passwordReset);
        return $ret;
    }

    public function register($email, $password)
    {
        $ret = null;
        $registerData = new Register();
        $registerData->setEmail($email);
        $registerData->setPassword($password);
        $request = "{$this->siteApi}v0/register";
        $ret = $this->client->call("User", $request, HttpMethodEnum::POST, $registerData);
        return $ret;
    }

    public function finishRegistration($uuid)
    {
        $request = "{$this->siteApi}v0/users/$uuid/finishRegistration";
        $resp = $this->client->call(null, $request, HttpMethodEnum::POST);
        $headers = $this->client->getHeaders();
        if(isset ($headers["X-Custom-Token"])){
            UserSession::setAccessToken($this->client->deserialize(base64_decode($headers["X-Custom-Token"]),'OAuthResponce'));
        }
    }

    public function getRegisteredUser($registrationId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$registrationId/registered";
        $ret = $this->client->call("User", $request);
        return $ret;
    }
    
    public function createPersonalInfo($userId, $personalInfo)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/personalInfo";
        $ret = $this->client->call("UserPersonalInformation", $request, HttpMethodEnum::POST, $personalInfo);
        return $ret;
    }
    
    public function updatePersonalInfo($userId, $personalInfo)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/personalInfo";
        $ret = $this->client->call("UserPersonalInformation", $request, HttpMethodEnum::PUT, $personalInfo);
        return $ret;
    }
    
    public function getPersonalInfo($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/personalInfo";
        $ret = $this->client->call("UserPersonalInformation", $request);
        return $ret;
    }
    
    public function createSecondaryLanguage($userId, $locale)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/secondaryLanguages";
        $ret = $this->client->call("Locale", $request, HttpMethodEnum::POST, $locale);
        return $ret;
    }
    
    public function getSecondaryLanguages($userId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/$userId/secondaryLanguages";
        $ret = $this->client->call(array("Locale"), $request);
        return $ret;
    }
    
    public function deleteSecondaryLanguage($userId, $locale)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/removeSecondaryLanguage/$userId/{$locale->getLanguageCode()}/{$locale->getCountryCode()}";
        $ret = $this->client->call(null, $request, HttpMethodEnum::DELETE);
        return $ret;
    }
    
    public function deleteUser($userId)
    {
        $request = "{$this->siteApi}v0/users/$userId";
        $this->client->call(null, $request, HttpMethodEnum::DELETE);
    }
    
    public function isBlacklistedForTask($userId, $taskId)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/users/isBlacklistedForTask/$userId/$taskId";
        $ret = $this->client->call(null, $request);
        return $ret;
    }
}
