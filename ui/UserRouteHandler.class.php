<?php

require_once 'app/models/Register.class.php';
require_once 'app/models/Login.class.php';
require_once 'app/models/PasswordResetRequest.class.php';
require_once 'app/models/PasswordReset.class.php';

class UserRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get('/', array($this, 'home'))->name('home');

        $app->get('/client/dashboard', array($middleware, 'authUserIsLoggedIn'), 
        array($this, 'clientDashboard'))->via('POST')->name('client-dashboard');

        $app->get('/register', array($this, 'register')
        )->via('GET', 'POST')->name('register');

        $app->get('/:uid/password/reset', array($this, 'passwordReset')
        )->via('POST')->name('password-reset');

        $app->get('/password/reset', array($this, 'passResetRequest')
        )->via('POST')->name('password-reset-request');
        
        $app->get('/logout', array($this, 'logout'))->name('logout');
        
        $app->get('/login', array($this, 'login')
        )->via('GET','POST')->name('login');

        $app->get('/profile/:user_id', array($this, 'userPublicProfile')
        )->via('POST')->name('user-public-profile');

        $app->get('/profile', array($middleware, 'authUserIsLoggedIn'), 
        array($this, 'userPrivateProfile'))->via('POST')->name('user-private-profile');
    }

    public function home()
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        
        $request = APIClient::API_VERSION."/tags/topTags";
        $response = $client->call($request, HTTP_Request2::METHOD_GET, null,
                                    array('limit' => 30));        
        $top_tags = array();
        if($response) {
            foreach($response as $stdObject) {
                $top_tags[] = $client->cast('Tag', $stdObject);
            }
        }        

        $app->view()->appendData(array(
            'top_tags' => $top_tags,
            'current_page' => 'home'
        ));

        $current_user_id = UserSession::getCurrentUserID();
        
        if($current_user_id == null) {
            $tasks = $client->castCall(array("Task"), APIClient::API_VERSION."/tasks/top_tasks"
                                       ,HTTP_Request2::METHOD_GET, null,array('limit' => 10));
            if($tasks) {
                $app->view()->appendData(array('tasks' => $tasks));
            }
        } else {
            $url = APIClient::API_VERSION."/users/$current_user_id/top_tasks";
            $response = $client->call($url, HTTP_Request2::METHOD_GET, null,
                                    array('limit' => 10));
            
            $tasks = array();
            if($response) {
                foreach($response as $stdObject) {
                    $tasks[] = $client->cast('Task', $stdObject);
                }
            }

            if($tasks) {
                $app->view()->setData('tasks', $tasks);
            }

            $url = APIClient::API_VERSION."/users/$current_user_id/tags";
            $response = $client->call($url);
            
            $user_tags = array();
            if($response) {
                foreach($response as $stdObject) {
                    $user_tags[] = $client->cast('Tag', $stdObject);
                }
            }
            
            $app->view()->appendData(array(
                        'user_tags' => $user_tags
            ));
        }
        
        $app->render('index.tpl');
    }

    public function clientDashboard()
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $current_user_id    = UserSession::getCurrentUserID();
        $current_user;
        
        
        $request = APIClient::API_VERSION."/users/$current_user_id";
        $response = $client->call($request);
        $current_user = $client->cast('User', $response);
        
        
        if (is_null($current_user_id)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }

        $my_organisations = array();
        $url = APIClient::API_VERSION."/users/$current_user_id/orgs";
        $response = $client->call($url);
        if($response) {
            foreach($response as $stdObject) {
                $my_organisations[] = $client->cast('Organisation', $stdObject);
            }
        }
        //$my_organisations = (array)$client->call($url);
        
        $org_tasks = array();
        $orgs = array();
        foreach($my_organisations as $org) {
            //$url = APIClient::API_VERSION."/orgs/$org_id";
            //$org_data = $client->call($url);
            $org = $client->cast('Organisation', $org);

            $url = APIClient::API_VERSION."/orgs/{$org->getId()}/tasks";
            $org_tasks_data = $client->call($url);        
            $my_org_tasks = array();
            if($org_tasks_data) {
                foreach($org_tasks_data as $stdObject) {
                    $my_org_tasks[] = $client->cast('Task', $stdObject);
                }
            } else {
                // If no org tasks, set to null
                $my_org_tasks = null;
            }   
            
            $request = APIClient::API_VERSION."/tags/topTags";
            $response = $client->call($request, HTTP_Request2::METHOD_GET, null,
                                        array('limit' => 30));        
            $top_tags = array();
            if($response) {
                foreach($response as $stdObject) {
                    $top_tags[] = $client->cast('Tag', $stdObject);
                }
            }            

            $org_tasks[$org->getId()] = $my_org_tasks;
            $orgs[$org->getId()] = $org;
        }    
        
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->track)) {
                $task_id = $post->task_id;
                $url = APICLient::API_VERSION."/tasks/$task_id";
                $response = $client->call($url);
                $task = $client->cast('Task', $response);

                $task_title = '';
                if($task->getTitle() != '') {
                    $task_title = $task->getTitle();
                } else {
                    $task_title = "task ".$task->getTaskId();
                }
                if($post->track == "Ignore") {
                   
                    $request = APIClient::API_VERSION."/users/$current_user_id/tracked_tasks/$task_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_DELETE);                    
                    
                    if($response) {
                        $app->flashNow('success', 'No longer receiving notifications from '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to unsubscribe from '.$task_title.'\'s notifications');
                    }                    
                } elseif($post->track == "Track") {
                    
                    $request = APIClient::API_VERSION."/users/$current_user_id/tracked_tasks/$task_id";
                    $response = $client->call($request, HTTP_Request2::METHOD_PUT);     
                    
                    if($response) {
                        $app->flashNow('success', 'You will now receive notifications for '.$task_title.'.');
                    } else {
                        $app->flashNow('error', 'Unable to subscribe to '.$task_title.'.');
                    }
                } else {
                    $app->flashNow('error', 'Invalid POST type');
                }
            }
        }
        if(count($org_tasks) > 0) {
            
            $templateData = array();
            foreach($org_tasks as $org=>$taskArray){
                $taskData = array();
                if($taskArray){
                    foreach($taskArray as $task){
                        $temp = array();
                        $temp['task']=$task;
                        $temp['translated']=$client->call(APIClient::API_VERSION."/tasks/{$task->getTaskId()}/version")>0;
                        $temp['taskClaimed']=$client->call(APIClient::API_VERSION."/tasks/{$task->getTaskId()}/claimed")==1;//$task_dao->taskIsClaimed($task->getTaskId());
                        $temp['userSubscribedToTask']=$client->call(APIClient::API_VERSION."/users/subscribedToTask/".UserSession::getCurrentUserID()."/{$task->getTaskId()}")==1;
                        $taskData[]=$temp;
                    }
                }
                $templateData[$org]=$taskData;
            }
            
            $app->view()->appendData(array(
                'orgs' => $orgs
                ,'templateData' => $templateData
            ));
        }
        
        $app->view()->appendData(array(
            'current_page'  => 'client-dashboard'
        ));
        $app->render('client.dashboard.tpl');
    }

    public function register()
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        
        $tempSettings=new Settings();
        $use_openid = $tempSettings->get("site.openid");
        $app->view()->setData('openid',$use_openid);
        if(isset($use_openid)) {
            if($use_openid == 'y' || $use_openid == 'h') {
                $extra_scripts = "
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/jquery-1.2.6.min.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/openid.css\" />";
                $app->view()->appendData(array('extra_scripts' => $extra_scripts));
            }   
        }   
        $error = null;
        $warning = null;
        if (isValidPost($app)) {
            $post = (object)$app->request()->post();
            
            if (!User::isValidEmail($post->email)) {
                $error = 'The email address you entered was not valid. Please cheak for typos and try again.';
            } elseif (!User::isValidPassword($post->password)) {
                $error = 'You didn\'t enter a password. Please try again.';
            }
            
            if (is_null($error)) {

                $request = APIClient::API_VERSION."/register";
                $response = $client->call($request, HTTP_Request2::METHOD_POST, new Register($post->email, $post->password));                
                if($response) {
                
                    $request = APIClient::API_VERSION."/login";             
                    $user = $client->call($request, HTTP_Request2::METHOD_POST, new Login($post->email, $post->password));                

                    try {
                        
                        if(!is_array($user) && !is_null($user)) {
                            $user = $client->cast("User", $user);
                            UserSession::setSession($user->getUserId());
                        } else {
                            throw new InvalidArgumentException('Sorry, the  password or username entered is incorrect. Please check the credentials used and try again.');    
                        }                    
                                       
                        
                        if(isset($_SESSION['previous_page'])) {
                            if(isset($_SESSION['old_page_vars'])) {
                                $app->redirect($app->urlFor($_SESSION['previous_page'], $_SESSION['old_page_vars']));
                            } else {
                                $app->redirect($app->urlFor($_SESSION['previous_page']));
                            }
                        }
                        $app->redirect($app->urlFor('home'));
                    } catch (InvalidArgumentException $e) {
                        $error = '<p>Unable to log in. Please check your email and password.';
                        $error .= ' <a href="' . $app->urlFor('login') . '">Try logging in again</a>';
                        $error .= ' or <a href="'.$app->urlFor('register').'">register</a> for an account.</p>';
                        $error .= '<p>System error: <em>' . $e->getMessage() .'</em></p>';

                        $app->flash('error', $error);
                        $app->redirect($app->urlFor('login'));
                        echo $error;                                        
                    }
                } else {
                    $warning = 'You have already created an account. <a href="' . $app->urlFor('login') . '">Please log in.</a>';
                }
            }
        }
        if ($error !== null) {
            $app->view()->appendData(array('error' => $error));
        }
        if ($warning !== null) {
            $app->view()->appendData(array('warning' => $warning));
        }
        $app->render('register.tpl');
    }

    public function passwordReset($uid)
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        
        //$user_dao = new UserDao();
        
        /*
        $request = APIClient::API_VERSION."/users/$uid/passwordResetRequest";
        $response = $client->call($request);        
        $user_obj = $client->cast('User', $response);
        */
        //$reset_request = $user_obj->
        
        //$my_org_tasks = array();
        //if($org_tasks_data) {
            //foreach($org_tasks_data as $stdObject) {
                //$my_org_tasks[] = $client->cast('Task', $stdObject);
            //}
        //}
        
        $request = APIClient::API_VERSION."/password_reset/$uid";
        $response = $client->call($request);        
        $reset_request = $client->cast('PasswordResetRequest', $response);
        // v0/password_reset/:key/
        
        //$reset_request = $user_dao->getPasswordResetRequests(array('uid' => $uid));     //wait for API support

        if($reset_request->getUserID()== '') {
            $app->flash('error', "Incorrect Unique ID. Are you sure you copied the URL correctly?");
            $app->redirect($app->urlFor('home'));
        }
        
        $user_id = $reset_request->getUserID();
        $app->view()->setData("uid",$uid);
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();

            if(isset($post->new_password) && User::isValidPassword($post->new_password)) {
                if(isset($post->confirmation_password) && 
                        $post->confirmation_password == $post->new_password) {
                    //if($user_dao->changePassword($user_id, $post->new_password)) {
                        //$user_dao->removePasswordResetRequest($user_id);
                    // HttpMethodEnum::POST, '/v0/password_reset(:format)/
                    $request = APIClient::API_VERSION."/password_reset";
                    $response = $client->call($request, HTTP_Request2::METHOD_POST, new PasswordReset($post->new_password, $uid));                     
                    
                    if($response) { 
                    
                        $app->flash('success', "You have successfully changed your password");
                        $app->redirect($app->urlFor('home'));
                    } else {
                        $app->flashNow('error', "Unable to change Password");
                    }
                } else {
                    $app->flashNow('error', "The passwords entered do not match.
                                        Please try again.");
                }
            } else {
                $app->flashNow('error', "Please check the password provided, and try again. It was not found to be valid.");
            }
        }        
        $app->render('password-reset.tpl');
    }

    public function passResetRequest()
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        
        //$user_dao = new UserDao();

        if($app->request()->isPost()) {
            $post = (object)$app->request()->post();
            if(isset($post->password_reset)) {
                if(isset($post->email_address) && $post->email_address != '')       //wait for API support
                {
                    $request = APIClient::API_VERSION."/users/getByEmail/{$post->email_address}";
                    $response = $client->call($request, HTTP_Request2::METHOD_GET);
                    $user = $client->cast('User', $response); 
                    
                    if($user) {  
                        $request = APIClient::API_VERSION."/users/{$user->getUserId()}/passwordResetRequest";
                        $hasUserRequestedPwReset = $client->call($request, HTTP_Request2::METHOD_GET);
                        
                        if (!$hasUserRequestedPwReset) {                            
                            $request = APIClient::API_VERSION."/users/{$user->getUserId()}/passwordResetRequest";
                            $response = $client->call($request, HTTP_Request2::METHOD_POST);                            
                            $app->flash('success', "Password reset request sent. Check your email
                                                    for further instructions.");
                            $app->redirect($app->urlFor('home'));
                        } else {
                            $app->flashNow('info', "Password reset request has already been sent.
                                                     Follow the link in the email that was sent to
                                                     you to reset your password");
                        }
                    } else {
                        $app->flashNow("error", "Please enter a valid email address");
                    }
                } else {
                    $app->flashNow("error", "Please enter a valid email address");
                }
            }
        }
        $app->render('user.reset-password.tpl');
    }
    
    public function logout()
    {
        $app = Slim::getInstance();
        UserSession::destroySession();    //TODO revisit when oauth is in place
        $app->redirect($app->urlFor('home'));
    }

    public function login()
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        
        $error = null;
        $tempSettings=new Settings();
        $openid = new LightOpenID($tempSettings->get("site.url"));
        $use_openid = $tempSettings->get("site.openid");
        $app->view()->setData('openid', $use_openid);
        if(isset($use_openid)) {
            if($use_openid == 'y' || $use_openid == 'h') {
                $extra_scripts = "
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/jquery-1.2.6.min.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-jquery.js\"></script>
                    <script type=\"text/javascript\" src=\"".$app->urlFor("home")."resources/bootstrap/js/openid-en.js\"></script>
                    <link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"".$app->urlFor("home")."resources/css/openid.css\" />";
                $app->view()->appendData(array('extra_scripts' => $extra_scripts));
            }
        }
        
        try {
            if (isValidPost($app)){
                $post = (object)$app->request()->post();

                if(isset($post->login)) {

                    $request = APIClient::API_VERSION."/login";             
                    $user = $client->call($request, HTTP_Request2::METHOD_POST, new Login($post->email, $post->password)); 
                    if(!is_array($user) && !is_null($user)) {
                        $user = $client->cast("User", $user);
                        UserSession::setSession($user->getUserId());
                    } else {
                        throw new InvalidArgumentException('Sorry, the  password or username entered is incorrect. Please check the credentials used and try again.');    
                    }
                    
                    $app->redirect($app->urlFor("home"));
                } elseif(isset($post->password_reset)) {
                    $app->redirect($app->urlFor('password-reset-request'));
                }
            } elseif($app->request()->isPost()||$openid->mode){
                $this->OpenIDLogin($openid,$app);
                $app->redirect($app->urlFor("home"));
            }
            $app->render('login.tpl');
        } catch (InvalidArgumentException $e) {
            $error = '<p>Unable to log in. Please check your email and password.';
            $error .= ' <a href="' . $app->urlFor('login') . '">Try logging in again</a>';
            $error .= ' or <a href="'.$app->urlFor('register').'">register</a> for an account.</p>';
            $error .= '<p>System error: <em>' . $e->getMessage() .'</em></p>';
            
            $app->flash('error', $error);
            $app->redirect($app->urlFor('login'));
            echo $error;
        }
    }
    
    public function OpenIDLogin($openid,$app) {
       
        if(!$openid->mode) {
            try {
            $openid->identity = $openid->data['openid_identifier'];
            $openid->required = array('contact/email');
            $url =$openid->authUrl();
            $app->redirect($openid->authUrl());
            }catch(ErrorException $e) {
                echo $e->getMessage();
            }
        } elseif($openid->mode == 'cancel') {
            throw new InvalidArgumentException('User has canceled authentication!');
            return false;
        } else {
            $retvals= $openid->getAttributes();
            if($openid->validate()){
                $client = new APIClient();
                $request = APIClient::API_VERSION."/users/getByEmail/{$retvals['contact/email']}";
                $response = $client->call($request);
                if (!is_object($response)&&!is_array($response)) {
                    $request = APIClient::API_VERSION."/register";
                    $response = $client->call($request, HTTP_Request2::METHOD_POST, new Register($retvals['contact/email'],md5($retvals['contact/email'])));                    
                }
                $user = $client->cast("User", $response);
                UserSession::setSession($user->getUserId());
            }
            return true;
        }
    }    
    

    public static function userPrivateProfile()
    {
        $app = Slim::getInstance();
        $client = new APIClient();
        $user_id = UserSession::getCurrentUserID();
        
        $url = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($url);
        $user = $client->cast('User', $response);
        $languages = TemplateHelper::getLanguageList();      //wait for API support
        $countries = TemplateHelper::getCountryList();       //wait for API support
        
        if (!is_object($user)) {
            $app->flash('error', 'Login required to access page');
            $app->redirect($app->urlFor('login'));
        }
        
        if($app->request()->isPost()) {
            $displayName = $app->request()->post('name');
            if($displayName != NULL) {
                $user->setDisplayName($displayName);
            }
            
            $userBio = $app->request()->post('bio');
            if($userBio != NULL) {
                $user->setBiography($userBio);
            }
            
            $nativeLang = $app->request()->post('nLanguage');
            $langCountry= $app->request()->post('nLanguageCountry');
            if($nativeLang != NULL&&$langCountry!= NULL) {
                $user->setNativeLanguageID($nativeLang);
                $user->setNativeRegionID($langCountry);

                $badge_id = Badge::NATIVE_LANGUAGE;
                $url = APIClient::API_VERSION."/badges/$badge_id";
                $response = $client->call($url);
                $badge = $client->cast('Badge', $response);
                
                $request = APIClient::API_VERSION."/users/$user_id/badges";
                $client->call($request, HTTP_Request2::METHOD_POST, $badge);               

            }
            
            $request = APIClient::API_VERSION."/users/$user_id";
            $client->call($request, HTTP_Request2::METHOD_PUT, $user);
            
            if($user->getDisplayName() != '' && $user->getBiography() != ''
                    && $user->getNativeLanguageID() != '' && $user->getNativeRegionID() != '') {

                $badge_id = Badge::PROFILE_FILLER;
                $url = APIClient::API_VERSION."/badges/$badge_id";
                $response = $client->call($url);
                $badge = $client->cast('Badge', $response);
                
                $request = APIClient::API_VERSION."/users/$user_id/badges";
                $response = $client->call($request, HTTP_Request2::METHOD_POST, $badge); 
            
            }
            
            $app->redirect($app->urlFor('user-public-profile', array('user_id' => $user->getUserId())));
        }
        
        $app->view()->setData('languages',$languages);
        $app->view()->setData('countries',$countries);
        
       
        $app->render('user-private-profile.tpl');
    }

    public static function userPublicProfile($user_id)
    {
        $app = Slim::getInstance();
        $client = new APIClient();

        $url = APIClient::API_VERSION."/users/$user_id";
        $response = $client->call($url);
        $user = $client->cast('User', $response);
        $user_id = $user->getUserId();
        
        if($app->request()->isPost()) {
            $post = (object) $app->request()->post();
            
            if(isset($post->badge_id) && $post->badge_id != '') {
                $badge_id = $post->badge_id;
                $request = APIClient::API_VERSION."/users/$user_id/badges/$badge_id";
                $response = $client->call($request, HTTP_Request2::METHOD_DELETE);                 
            }
                
            if(isset($post->revoke)) {
                $org_id = $post->org_id;
                $request = APIClient::API_VERSION."/users/leaveOrg/$user_id/$org_id";
                $response = $client->call($request, HTTP_Request2::METHOD_DELETE); 
            } 
        }
                    
        $activeJobs = array();        
        $request = APIClient::API_VERSION."/users/$user_id/tasks";
        $response = $client->call($request);
        
        if($response) {
            foreach($response as $stdObject) {
                $activeJobs[] = $client->cast('Task', $stdObject);
            }
        }

        $archivedJobs = array();
        $request = APIClient::API_VERSION."/users/$user_id/archived_tasks";
        $response = $client->call($request, HTTP_Request2::METHOD_GET, null, array('limit' => 10 )); 
        
        if($response) {
            foreach($response as $stdObject) {
                $archivedJobs[] = $client->cast('Task', $stdObject);
            }
        }        
         
        $user_tags = array();
        $request = APIClient::API_VERSION."/users/$user_id/tags";
        $response = $client->call($request);
        
        if($response) {
            foreach($response as $stdObject) {
                $user_tags[] = $client->cast('Tag', $stdObject);
            }
        }            
        
        $request = APIClient::API_VERSION."/users/$user_id/orgs";
        $orgs = $client->call($request);        
        
        $orgList = array();
        if(count($orgs) > 0) {
            foreach ($orgs as $orgObjs) {
                $orgList[] = $client->cast('Organisation', $orgObjs);
            }
        }
        
        $request = APIClient::API_VERSION."/users/$user_id/badges";
        $badgeList = $client->call($request, HTTP_Request2::METHOD_GET);         
        $badges = array();
        if($badgeList) {
            foreach($badgeList as $stdObject) {
                $badges[] = $client->cast('Badge', $stdObject);
            }      
        }
        
            
        $extra_scripts = "<script type=\"text/javascript\" src=\"".$app->urlFor("home");
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
              
        $app->view()->setData('orgList',  $orgList);
        $app->view()->appendData(array('badges' => $badges,
                                    'current_page' => 'user-profile',
                                    'activeJobs' => $activeJobs,
                                    'archivedJobs' => $archivedJobs,
                                    'user_tags' => $user_tags,
                                    'this_user' => $user,
                                    'extra_scripts' => $extra_scripts
        ));
                
        if(UserSession::getCurrentUserID() === $user_id) {
            $app->view()->appendData(array('private_access' => true));
        }
                    
        $app->render('user-public-profile.tpl');
    }
    

    public static function isLoggedIn()
    {
        return (!is_null(UserSession::getCurrentUserId()));
    }     
}
