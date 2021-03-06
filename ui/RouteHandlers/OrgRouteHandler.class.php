<?php

class OrgRouteHandler
{
    public function init()
    {
        $app = Slim::getInstance();
        $middleware = new Middleware();

        $app->get("/org/create/", array($middleware, "authUserIsLoggedIn")
        , array($this, "createOrg"))->via("POST")->name("create-org");
        
        $app->get("/org/dashboard/", array($middleware, "authUserIsLoggedIn")
        , array($this, "orgDashboard"))->via("POST")->name("org-dashboard");        

        $app->get("/org/:org_id/request/", array($middleware, "authUserIsLoggedIn")
        , array($this, "orgRequestMembership"))->name("org-request-membership");

        $app->get("/org/:org_id/request/:user_id/:accept/", array($middleware, "authUserForOrg")
        , array($this, "orgProcessRequest"))->name("org-process-request");

        $app->get("/org/:org_id/request/queue/", array($middleware, "authUserForOrg")
        , array($this, "orgRequestQueue"))->via("POST")->name("org-request-queue");

        $app->get("/org/:org_id/private/", array($middleware, "authUserForOrg")
        , array($this, "orgPrivateProfile"))->via("POST")->name("org-private-profile");

        $app->get("/org/:org_id/profile/", array($middleware, "authUserIsLoggedIn")
        , array($this, "orgPublicProfile"))->via("POST")->name("org-public-profile");

        $app->get("/org/:org_id/manage/:badge_id/", array($middleware, "authUserForOrg")
        , array($this, "orgManageBadge"))->via("POST")->name("org-manage-badge");

        $app->get("/org/:org_id/create/badge/", array($middleware, "authUserForOrg")
        , array($this, "orgCreateBadge"))->via("POST")->name("org-create-badge");

        $app->get("/org/search/", array($middleware, "authUserIsLoggedIn")
        , array($this, "orgSearch"))->via("POST")->name("org-search");
        
        $app->get("/org/:org_id/edit/:badge_id/", array($middleware, "authUserForOrg")
        , array($this, "orgEditBadge"))->via("POST")->name("org-edit-badge");         

        $app->get("/org/:org_id/task/:task_id/complete/", array($middleware, "authUserForOrg")
        , array($this, "orgTaskComplete"))->name("org-task-complete");

        $app->get("/org/:org_id/task/:task_id/review/", array($middleware, "authUserForOrg"),
        array($this, "orgTaskReview"))->via("POST")->name("org-task-review");

        $app->get("/org/:org_id/task/:task_id/reviews/", array($middleware, "authUserForOrg"),
        array($this, "orgTaskReviews"))->name("org-task-reviews");
    }

    public function createOrg()
    {
        $app = Slim::getInstance();   
        
        if ($post = $app->request()->post()) {
            $nameErr = null;

            $org = new Organisation();

            if(isset($post["orgName"]) && $post["orgName"] != '') {
                $org->setName($post['orgName']); 
            } else {
                $nameErr = Localisation::getTranslation(Strings::CREATE_ORG_1);
            }
            
            if(isset($post["homepage"])) $org->setHomePage($post["homepage"]); 
            if(isset($post["biography"])) $org->setBiography($post["biography"]);
            if(isset($post["address"])) $org->setAddress($post["address"]);
            if(isset($post["city"])) $org->setCity($post["city"]);
            if(isset($post["country"])) $org->setCountry($post["country"]);
            if(isset($post["email"])) $org->setEmail($post["email"]);

            $regionalFocus = array();
            if(isset($post["africa"])) $regionalFocus[] = "Africa";             
            if(isset($post["asia"])) $regionalFocus[] = "Asia";             
            if(isset($post["australia"])) $regionalFocus[] = "Australia"; 
            if(isset($post["europe"])) $regionalFocus[] .= "Europe"; 
            if(isset($post["northAmerica"])) $regionalFocus[] .= "North-America"; 
            if(isset($post["southAmerica"])) $regionalFocus[] .= "South-America"; 
            
            if(!empty($regionalFocus)) {
                $org->setRegionalFocus(implode(",", $regionalFocus));
            }
            
            if(is_null($nameErr)) {
                $user_id = UserSession::getCurrentUserID();
                $orgDao = new OrganisationDao();
                $organisation = $orgDao->getOrganisationByName($org->getName());

                if (!$organisation) {
                    $new_org = $orgDao->createOrg($org, $user_id);
                    if ($new_org) {
                        $org_name = $org->getName();
                        $app->flash("success", sprintf(Localisation::getTranslation(Strings::CREATE_ORG_2), $org_name));
                        $app->redirect($app->urlFor("org-dashboard"));
                    } else {
                        $app->flashNow("error", Localisation::getTranslation(Strings::CREATE_ORG_3));
                    }
                } else {
                    $org_name = $org->getName();
                    $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::CREATE_ORG_4), $org_name));
                }         
            } else {
                $app->view()->appendData(array(
                    "org"     => $org,
                    "nameErr" => $nameErr
                ));
            }
        }   
        $app->view()->appendData(array(
            "org"     => null
        ));
        $app->render("org/create-org.tpl");
    }    

    public function orgDashboard()
    {
        $app = Slim::getInstance();
        $current_user_id    = UserSession::getCurrentUserID();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();
        $tagDao = new TagDao();
        $projectDao = new ProjectDao();
        
        $current_user = $userDao->getUser($current_user_id);        
        $my_organisations = $userDao->getUserOrgs($current_user_id);
        $org_projects = array();
        
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            if (isset($post['track'])) {
                $project_id = $post['project_id'];
                $project = $projectDao->getProject($project_id);

                $project_title = "";
                if ($project->getTitle() != "") {
                    $project_title = $project->getTitle();
                } else {
                    $project_title = "project ".$project->getId();
                }
                if ($post['track'] == "Ignore") {
                    $success = $userDao->untrackProject($current_user_id, $project_id);                    
                    if ($success) {
                        $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::ORG_DASHBOARD_5), $project_title));
                    } else {
                        $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_DASHBOARD_6), $project_title));
                    }                    
                } elseif ($post['track'] == "Track") {
                    $success = $userDao->trackProject($current_user_id, $project_id);                    
                    if ($success) {
                        $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::ORG_DASHBOARD_7), $project_title));
                    } else {
                        $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_DASHBOARD_8), $project_title));
                    }
                }
            }
        }
        
        if($my_organisations){
            $orgs = array();
            $templateData = array();
            foreach ($my_organisations as $org) {
                $my_org_projects = $orgDao->getOrgProjects($org->getId());
                $org_projects[$org->getId()] = $my_org_projects;
                $orgs[$org->getId()] = $org;

                $taskData = array();
                if ($my_org_projects) {
                    foreach ($my_org_projects as $project) {
                        $temp = array();
                        $temp['project'] = $project;
                        $temp['userSubscribedToProject'] = $userDao->isSubscribedToProject(
                                UserSession::getCurrentUserID(), $project->getId());
                        $taskData[]=$temp;
                    }
                } else {
                    $taskData = null;
                }
                $templateData[$org->getId()] = $taskData;
            }

            $app->view()->appendData(array(
                "orgs" => $orgs,
                "templateData" => $templateData
            ));
        }
        
        $app->view()->appendData(array(
            "current_page"  => "org-dashboard"
        ));
        $app->render("org/org.dashboard.tpl");
    }    
    

    public function orgRequestMembership($org_id)
    {
        $app = Slim::getInstance();
        $userDao = new UserDao();
        $orgDao = new OrganisationDao();

        $userId = UserSession::getCurrentUserID();
        $user = $userDao->getUser($userId);
        $user_orgs = $userDao->getUserOrgs($userId);
        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
            $requestMembership = $orgDao->createMembershipRequest($org_id, $userId);
            if ($requestMembership) {
                $app->flash("success", Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_11));
            } else {
                $app->flash("error", Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_12));
            }   
        } else {
            $app->flash("error", Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_13));
        }
        $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
    }

    public function orgRequestQueue($org_id)
    {
        $app = Slim::getInstance();
        $orgDao = new OrganisationDao();
        $userDao = new UserDao();

        $org = $orgDao->getOrganisation($org_id);
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if (isset($post['email'])) {
                if (TemplateHelper::isValidEmail($post['email'])) {       
                    $success = $orgDao->addMember($post['email'], $org_id);
                    if ($success) {
                        $app->flashNow("success", 
                                sprintf(Localisation::getTranslation(Strings::COMMON_SUCCESSFULLY_ADDED_MEMBER), 
                                            $post['email'], $org->getName()));
                    } else {
                        $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_20), $post['email']));
                    }   
                } else {
                    $app->flashNow("error", Localisation::getTranslation(Strings::COMMON_NO_VALID_EMAIL));
                }
            } elseif (isset($post['accept'])) {
                if ($user_id = $post['user_id']) {
                    $orgDao->acceptMembershipRequest($org_id, $user_id);
                } else {
                    $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_REQUEST_QUEUE_7), $user_id));
                }
            } elseif (isset($post['refuse'])) {
                if ($user_id = $post['user_id']) {
                    $orgDao->rejectMembershipRequest($org_id, $user_id);
                } else {
                    $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_REQUEST_QUEUE_7), $user_id));
                }
            }
        }
        $requests = $orgDao->getMembershipRequests($org_id);
        $user_list = array();
        if (count($requests) > 0) {
            foreach ($requests as $memRequest) {
                $user_list[] =  $userDao->getUser($memRequest->getId());
            }
        }
        
        $app->view()->setData("org", $org);
        $app->view()->appendData(array("user_list" => $user_list));
        
        $app->render("org/org.request_queue.tpl");
    }

    public function orgPrivateProfile($org_id)
    {
        $app = Slim::getInstance();
        $orgDao = new OrganisationDao();
        $org = $orgDao->getOrganisation($org_id);
        $userId = UserSession::getCurrentUserId();
        if($post = $app->request()->post()) {

            if (isset($post['updateOrgDetails'])) {
                if(isset($post['displayName'])) $org->setName($post['displayName']); 
                if(isset($post['homepage'])) $org->setHomePage($post['homepage']); 
                if(isset($post['biography'])) $org->setBiography($post['biography']);
                if(isset($post['address'])) $org->setAddress($post['address']);
                if(isset($post['city'])) $org->setCity($post['city']);
                if(isset($post['country'])) $org->setCountry($post['country']);
                if(isset($post['email'])) $org->setEmail($post['email']);

                $regionalFocus = array();
                if(isset($post["africa"])) $regionalFocus[] = "Africa";             
                if(isset($post["asia"])) $regionalFocus[] = "Asia";             
                if(isset($post["australia"])) $regionalFocus[] = "Australia"; 
                if(isset($post["europe"])) $regionalFocus[] .= "Europe"; 
                if(isset($post["northAmerica"])) $regionalFocus[] .= "North-America"; 
                if(isset($post["southAmerica"])) $regionalFocus[] .= "South-America"; 

                if(!empty($regionalFocus)) {
                    $org->setRegionalFocus(implode(",", $regionalFocus));
                }

                $orgDao->updateOrg($org); 
                $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org->getId())));
            }

            if (isset($post['deleteId'])) {

                $deleteId = $post["deleteId"];
                if ($deleteId) {
                    if ($orgDao->deleteOrg($org->getId())) {
                        $app->flash("success", sprintf(Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_17), $org->getName()));
                        $app->redirect($app->urlFor("home"));
                    } else {
                        $app->flashNow("error", Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_18));
                    }
                }
            }
        }
        

        $adminDao = new AdminDao();
        if ($adminDao->isOrgAdmin($org->getId(), $userId) || $adminDao->isSiteAdmin($userId)) {
            $app->view()->appendData(array('orgAdmin' => true));
        }
        
        $app->view()->setData("org", $org);        
        $app->render("org/org-private-profile.tpl");
    }

    public function orgPublicProfile($org_id)
    {
        $app = Slim::getInstance();
        $adminDao = new AdminDao();
        $orgDao = new OrganisationDao();
        $userDao = new UserDao();
        $badgeDao = new BadgeDao();

        $org = $orgDao->getOrganisation($org_id);
        if ($app->request()->isPost()) {
            $post = $app->request()->post();
                   
            if (isset($post['deleteBadge'])) {
                $badgeDao->deleteBadge($post['badge_id']);
            } 
            
            if (isset($post['title']) && isset($post['description'])) {
                if ($post['title'] == "" || $post['description'] == "") {
                    $app->flash("error", sprintf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_19)));
                } else {
                    $badge = new Badge();
                    $badge->setId($post['badge_id']);
                    $badge->setTitle($post['title']);
                    $badge->setDescription($post['description']);
                    $badge->setOwnerId(null);
                    $badgeDao->updateBadge($badge); 
                    $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
                }
            }
            
            if (isset($post['email'])) {
                if (TemplateHelper::isValidEmail($post['email'])) {       
                    $user = $userDao->getUserByEmail($post['email']);
                
                    if (!is_null($user)) {
                        $user_orgs = $userDao->getUserOrgs($user->getId());
                        if ($user->getDisplayName() != "") {
                            $user_name = $user->getDisplayName();
                        } else {
                            $user_name = $user->getEmail();
                        }   
                        if (is_null($user_orgs) || !in_array($org_id, $user_orgs)) {
                            $orgDao->acceptMembershipRequest($org_id, $user->getId());
                            if ($org->getName() != "") {
                                $org_name = $org->getName();
                            } else {
                                $org_name = "Organisation $org_id";
                            }   
                            $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::COMMON_SUCCESSFULLY_ADDED_MEMBER), $user_name, $org_name));
                        } else {
                            $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_20), $user_name));
                        }   
                    } else {
                        $email = $post['email'];
                        $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_21), $email));
                    }
                } else {
                    $app->flashNow("error", Localisation::getTranslation(Strings::COMMON_NO_VALID_EMAIL));
                }
            } elseif (isset($post['accept'])) {
                if ($user_id = $post['user_id']) {
                    if ($orgDao->acceptMembershipRequest($org_id, $user_id)){
                        $user = $userDao->getUser($user_id);
                        $user_name = $user->getDisplayName();
                        $org_name = $org->getName();    
                        $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_23), $app->urlFor("user-public-profile", array("user_id" => $user_id)) ,$user_name, $org_name));
                    } else {
                        $app->flashNow("error", Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_24));
                    }

                } else {
                    $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::COMMON_INVALID_USERID), $user_id));
                }
            } elseif (isset($post['refuse'])) {
                if ($user_id = $post['user_id']) {
                    $orgDao->rejectMembershipRequest($org_id, $user_id);
                    $user = $userDao->getUser($user_id);
                    $user_name = $user->getDisplayName();
                    $app->flashNow("success", printf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_25), $app->urlFor("user-public-profile", array("user_id" => $user_id)), $user_name));
                } else {
                    $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::COMMON_INVALID_USERID), $user_id));
                }
            } elseif (isset($post['revokeUser'])) {
                $userId = $post['revokeUser'];
                $user = $userDao->getUser($userId);
                if ($user) {
                    $userName = $user->getDisplayName();
                    if ($userDao->leaveOrganisation($userId, $org_id)) {
                        $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_26), $app->urlFor("user-public-profile", array("user_id" => $userId)), $userName));
                    } else {
                        $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_27), $app->urlFor("user-public-profile", array("user_id" => $userId)), $userName));
                    }
                } else {
                    $app->flashNow("error", Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_28));
                }
            } else if(isset($post['revokeOrgAdmin'])) {
                $userId = $post['revokeOrgAdmin'];
                $adminDao->removeOrgAdmin($userId, $org_id);
                
            } else if(isset($post['makeOrgAdmin'])) {
                $userId = $post['makeOrgAdmin'];
                $adminDao->createOrgAdmin($userId, $org_id);
            }
        }       
        
        $currentUser = $userDao->getUser(UserSession::getCurrentUserId());
        $isMember = false;
        $orgMemberList = $orgDao->getOrgMembers($org_id);
        if (count($orgMemberList) > 0) {
            foreach ($orgMemberList as $member) {
                if ($currentUser->getId() ==  $member->getId()) {
                    $isMember = true;
                }
            }
        }

        $adminAccess = false;
        if ($adminDao->isSiteAdmin($currentUser->getId()) == 1 || $adminDao->isOrgAdmin($org->getId(), $currentUser->getId()) == 1) {
            $adminAccess = true;
        }

        $org_badges = array();
        $user_list = array();

        if ($isMember || $adminAccess) {
            $requests = $orgDao->getMembershipRequests($org_id);
            if (count($requests) > 0) {
                foreach ($requests as $memRequest) {
                    $user = $userDao->getUser($memRequest->getUserId());
                    $user_list[] = $user;
                }
            }

            $org_badges = $orgDao->getOrgBadges($org_id);
        
            if($orgMemberList) {
                foreach($orgMemberList as $orgMember) {
                    if($adminDao->isOrgAdmin($org_id, $orgMember->getId())) {
                        $orgMember['orgAdmin'] = true;
                    }
                }
            }
        }

        $siteName = Settings::get("site.name");
        $app->view()->setData("current_page", "org-public-profile");
        $app->view()->appendData(array(
                "org" => $org,
                'isMember'  => $isMember,
                'orgMembers' => $orgMemberList,
                'adminAccess' => $adminAccess,
                "org_badges" => $org_badges,
                'siteName' => $siteName,
                "membershipRequestUsers" => $user_list
        ));
        
        $app->render("org/org-public-profile.tpl");
    }

    public function orgManageBadge($org_id, $badge_id)
    {
        $app = Slim::getInstance();
        $badgeDao = new BadgeDao();
        $userDao = new UserDao();

        $badge = $badgeDao->getBadge($badge_id);
        $extra_scripts = "<script type=\"text/javascript\" src=\"{$app->urlFor("home")}";
        $extra_scripts .= "resources/bootstrap/js/confirm-remove-badge.js\"></script>";
        $app->view()->setData("badge", $badge);
        $app->view()->appendData(array(
                    "org_id"        => $org_id,
                    "extra_scripts" =>$extra_scripts
        ));

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if (isset($post['email']) && $post['email'] != "") {
                if (TemplateHelper::isValidEmail($post['email'])) {
                    $success = $userDao->assignBadge($post['email'], $badge->getId());
                    if ($success) {
                        $app->flashNow("success", 
                            sprintf(Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_29), $badge->getTitle(), $post['email']));
                    } else {
                        $app->flashNow("error", sprintf(Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_30), $post['email']));
                    }
                } else {
                    $app->flashNow("error", Localisation::getTranslation(Strings::COMMON_NO_VALID_EMAIL));
                }
            } elseif (isset($post['user_id']) && $post['user_id'] != "") {
                $user_id = $post['user_id'];
                $user = $userDao->getUser($user_id);
                $userDao->removeUserBadge($user_id, $badge_id);
                $user_name = "";
                if ($user->getDisplayName() != "") {
                    $user_name = $user->getDisplayName();
                } else {
                    $user_name = $user->getEmail();
                }
                $app->flashNow("success", sprintf(Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_32), $user_name));
            }
        }
    
        $user_list = $badgeDao->getUserWithBadge($badge_id);

        $app->view()->appendData(array(
            "user_list" => $user_list
        ));
        
        $app->render("org/org.manage-badge.tpl");
    }

    public function orgCreateBadge($org_id)
    {
        $app = Slim::getInstance();
        $badgeDao = new BadgeDao();

        if (isValidPost($app)) {
            $post = $app->request()->post();
            
            if ($post['title'] == "" || $post['description'] == "") {
                $app->flashNow("error", Localisation::getTranslation(Strings::COMMON_ALL_FIELDS));
            } else {
                $badge = new Badge();
                $badge->setTitle($post['title']);
                $badge->setDescription($post['description']);
                $badge->setOwnerId($org_id);
                $badgeDao->createBadge($badge);                
                
                $app->flash("success", Localisation::getTranslation(Strings::ORG_CREATE_BADGE_33));
                $app->redirect($app->urlFor("org-public-profile", array("org_id" => $org_id)));
            }
        }
        
        $app->view()->setData("org_id", $org_id);        
        $app->render("org/org.create-badge.tpl");
    }

    public function orgSearch()
    {
        $app = Slim::getInstance();
        $orgDao = new OrganisationDao();
        $foundOrgs = array();

        if ($app->request()->isPost()) {
            $post = $app->request()->post();
            
            if (isset($post['search_name']) && $post['search_name'] != '') {                
                $foundOrgs = $orgDao->searchForOrgByName(urlencode($post['search_name']));
                if (count($foundOrgs) < 1) {
                    $app->flashNow("error", Localisation::getTranslation(Strings::ORG_SEARCH_34));
                }
                $app->view()->appendData(array('searchedText' => $post['search_name']));
            }

            if (isset($post['allOrgs'])) {
                $foundOrgs = $orgDao->getOrganisations();
            }
        }        

        $app->view()->appendData(array(
                    'foundOrgs'     => $foundOrgs
        ));

        $app->render("org/org-search.tpl");
    }
    
    public function orgEditBadge($org_id, $badge_id)
    {
        $app = Slim::getInstance();
        $badgeDao = new BadgeDao();

        $badge = $badgeDao->getBadge($badge_id);
        $app->view()->setData("badge", $badge);        
        $app->view()->appendData(array("org_id" => $org_id));        
        
        $app->render("org/org.edit-badge.tpl");        
    }    

    public function orgTaskComplete($orgId, $taskId)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $claimant = $taskDao->getUserClaimedTask($taskId);
        $claimantProfile = "";
        if ($claimant != null) {
            $claimantProfile = $app->urlFor("user-public-profile", array('user_id' => $claimant->getId()));
        }

        $task = $taskDao->getTask($taskId);
        $viewData = array(
                "task"              => $task,
                'claimant'          => $claimant,
                'claimantProfile'   => $claimantProfile,
                "orgId"             => $orgId
        );

        $app->view()->appendData($viewData);
        $app->render("org/org.task-complete.tpl");
    }

    public function orgTaskReview($orgId, $taskId)
    {
        $app = Slim::getInstance();
        $taskDao = new TaskDao();
        $userDao = new UserDao();

        $userId = UserSession::getCurrentUserID();
        $task = $taskDao->getTask($taskId);

        if ($app->request()->isPost()) {
            $post = $app->request()->post();

            if (isset($post['submitReview'])) {
                $review = new TaskReview();
                $review->setUserId($userId);
                $review->setTaskId($taskId);
                $review->setProjectId($task->getProjectId());

                $error = '';

                $id = $taskId;
                if (isset($post["corrections_$id"]) && ctype_digit($post["corrections_$id"])) {
                    $value = intval($post["corrections_$id"]);
                    if ($value > 0 && $value <= 5) {
                        $review->setCorrections($value);
                    } else {
                        $error = Localisation::getTranslation(Strings::ORG_TASK_REVIEW_35);
                    }
                }
                if (isset($post["grammar_$id"]) && ctype_digit($post["grammar_$id"])) {
                    $value = intval($post["grammar_$id"]);
                    if ($value > 0 && $value <= 5) {
                        $review->setGrammar($value);
                    } else {
                        $error = Localisation::getTranslation(Strings::ORG_TASK_REVIEW_36);
                    }
                }
                if (isset($post["spelling_$id"]) && ctype_digit($post["spelling_$id"])) {
                    $value = intval($post["spelling_$id"]);
                    if ($value > 0 && $value <= 5) {
                        $review->setSpelling($value);
                    } else {
                        $error = Localisation::getTranslation(Strings::ORG_TASK_REVIEW_37);
                    }
                }
                if (isset($post["consistency_$id"]) && ctype_digit($post["consistency_$id"])) {
                    $value = intval($post["consistency_$id"]);
                    if ($value > 0 && $value <= 5) {
                        $review->setConsistency($value);
                    } else {
                        $error = Localisation::getTranslation(Strings::ORG_TASK_REVIEW_38);
                    }
                }
                if (isset($post["comment_$id"]) && $post["comment_$id"] != "") {
                    $review->setComment($post["comment_$id"]);
                }
                
                if ($review->getProjectId() != null && $review->getUserId() != null && $error == '') {
                    if (!$taskDao->submitReview($review)) {
                        $error = sprintf(Localisation::getTranslation(Strings::ORG_TASK_REVIEW_39), $task->getTitle());
                    }
                }

                if ($error != '') {
                    $app->flashNow("error", $error);
                } else {
                    $app->flash("success", sprintf(Localisation::getTranslation(Strings::ORG_TASK_REVIEW_40), $task->getTitle()));
                    $app->redirect($app->urlFor('project-view', array("project_id" => $task->getProjectId())));
                }
            }

            if (isset($post['skip'])) {
                $app->redirect($app->urlFor("project-view", array(
                                'project_id' => $task->getProjectId())));
            }
        }

        $taskReview = $userDao->getUserTaskReviews($userId, $taskId);
        if (!is_null($taskReview)) {
            $app->flashNow("info", Localisation::getTranslation(Strings::ORG_TASK_REVIEW_41));
        }

        $translator = $taskDao->getUserClaimedTask($taskId);

        $formAction = $app->urlFor("org-task-review", array(
                    'org_id'    => $orgId,
                    'task_id'   => $taskId
        ));

        $extra_scripts = "";
        $extra_scripts .= "<script type='text/javascript'>";
        $extra_scripts .= "var taskIds = new Array();";
        $extra_scripts .= "taskIds[0] = $taskId;";
        $extra_scripts .= "</script>";
        
        $extra_scripts .= "<link rel=\"stylesheet\" href=\"{$app->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";
        $extra_scripts .= file_get_contents(__DIR__."/../js/review.js");

        $app->view()->appendData(array(
                    'extra_scripts' => $extra_scripts,
                    'task'      => $task,
                    'review'    => $taskReview,
                    'translator'=> $translator,
                    'formAction'=> $formAction
        ));

        $app->render("org/org.task-review.tpl");
    }

    public function orgTaskReviews($orgId, $taskId)
    {
        $app = Slim::getInstance();
        $viewData = array();
        $taskDao = new TaskDao();
        $task = $taskDao->getTask($taskId);
        $preReqTasks = array();
        $preReqs = $taskDao->getTaskPreReqs($taskId);
        $reviews = array();
        if (!is_null($preReqs) && count($preReqs) > 0) {
            foreach ($preReqs as $preReq) {
                $taskReviews = $taskDao->getTaskReviews($preReq->getId());
                if (!is_null($taskReviews) && count($taskReviews) > 0) {
                    $preReqTasks[] = $preReq;
                    $reviews[$preReq->getId()] = $taskReviews;
                }
            }
            $viewData['preReqTasks'] = $preReqTasks;
        } else {
            $projectDao= new ProjectDao();
            $project = $projectDao->getProject($task->getProjectId());
            
            $allProjectReviews = $projectDao->getProjectReviews($task->getProjectId());
            if (!is_null($allProjectReviews) && count($allProjectReviews) > 0) {
                $validReviews = array();
                foreach ($allProjectReviews as $projectReview) {
                    if ($projectReview->getTaskId() == null) {
                        $validReviews[] = $projectReview;
                    }
                }
                if (count($validReviews) > 0) {
                    $viewData['projectReviews'] = $validReviews;
                }
            }
            
            $dummyTask = new Task();        //Create a dummy task to hold the project info
            $dummyTask->setProjectId($task->getProjectId());
            $dummyTask->setTitle($project->getTitle());
            $viewData['projectData'] = $dummyTask;
        }

        $taskReviews = $taskDao->getTaskReviews($taskId);
        if (!is_null($taskReviews) && count($taskReviews) > 0) {
            $reviews[$taskId] = $taskReviews;
        }

        $extra_scripts = "<link rel=\"stylesheet\" href=\"{$app->urlFor("home")}ui/js/RateIt/src/rateit.css\"/>";
        $extra_scripts .= "<script>".file_get_contents(__DIR__."/../js/RateIt/src/jquery.rateit.min.js")."</script>";

        $viewData['task'] = $task;
        $viewData['reviews'] = $reviews;
        $viewData['extra_scripts'] = $extra_scripts;

        $app->view()->appendData($viewData);
        $app->render('org/org.task-reviews.tpl');
    }
}

$route_handler = new OrgRouteHandler();
$route_handler->init();
unset ($route_handler);
