<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../../Common/TaskTypeEnum.php';
require_once __DIR__.'/../UnitTestHelper.php';

class TaskDaoTest extends PHPUnit_Framework_TestCase
{
    public function testCreateTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        // Success
        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Failure - Duplicate Task
        $insertedTask2 = TaskDao::create($task);
        $this->assertNull($insertedTask2);
        
    }
    
    public function testUpdateTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());

        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        $task->setId($insertedTask->getId());
        $task->setTitle("Updated Title");
        $task->setComment("Updated Comment");
        $task->setWordCount(334455);
        $task->setDeadline("2025-03-29 19:25:12");
        
        $sourceLocale = new Locale();
        $sourceLocale->setLanguageCode("de");
        $sourceLocale->setCountryCode("DE");
        $task->setSourceLocale($sourceLocale);
                
        $targetLocale = new Locale();
        $targetLocale->setLanguageCode("es");
        $targetLocale->setCountryCode("ES");
        $task->setTargetLocale($targetLocale);
        
        $task->setPublished(0);
        
//        $i = 0;
//        foreach($insertedProject->getTag() as $tag) {
//            $task->setTag($tag, $i);
//            $i++;
//        }
        $task->setTaskStatus(3);
        $task->setTaskType(3);        
        $task->setCreatedTime("2030-07-14 12:24:02");
        
        // Success
        $updatedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $updatedTask);
        $this->assertEquals($insertedTask->getId(), $task->getId());
        $this->assertEquals($task->getTitle(), $updatedTask->getTitle());
        $this->assertEquals($task->getComment(), $updatedTask->getComment());
        $this->assertEquals($task->getWordCount(), $updatedTask->getWordCount());
        $this->assertEquals($task->getDeadline(), $updatedTask->getDeadline());
        
        $this->assertEquals($task->getSourceLocale()->getLanguageCode(), $updatedTask->getSourceLocale()->getLanguageCode());
        $this->assertEquals($task->getSourceLocale()->getCountryCode(), $updatedTask->getSourceLocale()->getCountryCode());
        $this->assertEquals($task->getTargetLocale()->getLanguageCode(), $updatedTask->getTargetLocale()->getLanguageCode());
        $this->assertEquals($task->getTargetLocale()->getCountryCode(), $updatedTask->getTargetLocale()->getCountryCode());
        $this->assertEquals($task->getPublished(), $updatedTask->getPublished());

    }
    
    public function testGetTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());

        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Success
        $retrievedTask = TaskDao::getTask($task->getId(),$task->getProjectId(), $task->getTitle(),$task->getWordCount(),
                $task->getSourceLocale()->getLanguageCode(),$task->getTargetLocale()->getLanguageCode(),$task->getCreatedTime(),
                $task->getSourceLocale()->getCountryCode(),$task->getTargetLocale()->getCountryCode(),$task->getComment(),$task->getTaskType(),
                $task->getTaskStatus(),$task->getPublished(),$task->getDeadline());
        
        $this->assertInstanceOf("Task", $retrievedTask[0]);
        $this->assertEquals($insertedTask->getId(), $retrievedTask[0]->getId());
        $this->assertEquals($task->getTitle(), $retrievedTask[0]->getTitle());
        $this->assertEquals($task->getComment(), $retrievedTask[0]->getComment());
        $this->assertEquals($task->getWordCount(), $retrievedTask[0]->getWordCount());
        $this->assertEquals($task->getDeadline(), $retrievedTask[0]->getDeadline());
        
        $this->assertEquals($task->getSourceLocale()->getLanguageCode(), $retrievedTask[0]->getSourceLocale()->getLanguageCode());
        $this->assertEquals($task->getSourceLocale()->getCountryCode(), $retrievedTask[0]->getSourceLocale()->getCountryCode());
        $this->assertEquals($task->getTargetLocale()->getLanguageCode(), $retrievedTask[0]->getTargetLocale()->getLanguageCode());
        $this->assertEquals($task->getTargetLocale()->getCountryCode(), $retrievedTask[0]->getTargetLocale()->getCountryCode());
        
        $this->assertEquals($task->getPublished(), $retrievedTask[0]->getPublished());       
        
        
        // Failure
        $nonExistantTask = TaskDao::getTask(999);
        $this->assertNull($nonExistantTask);
        
    }
    
    
    public function testDeleteTask()
    {        
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());

        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        $this->assertNotNull($insertedTask->getId());
        
        // Success
        $deletedTask = TaskDao::delete($insertedTask->getId());
        $this->assertEquals("1", $deletedTask);
        
        // Failure
        $deleteNonExistantTask = TaskDao::delete(999);
        $this->assertEquals("0", $deleteNonExistantTask);
    }
    
    public function testAddTaskPreReq()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2022-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2021-03-29 16:30:00", 22222, null, TaskTypeEnum::PROOFREADING);        

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask);
        
        $proofReadingTask = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $proofReadingTask);
        
        // Success
        $addTaskPreReq = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $addTaskPreReq);
        
        // Failure
        $addTaskPreReqDuplicate = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("0", $addTaskPreReqDuplicate);
    }
    
    public function testRemoveTaskPreReq()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2022-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2021-03-29 16:30:00", 22222, null, TaskTypeEnum::PROOFREADING);        

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask);
        
        $proofReadingTask = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $proofReadingTask);
        
        $addTaskPreReq = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $addTaskPreReq);
        
        // Success
        $removeTaskPreReq = TaskDao::removeTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $removeTaskPreReq);   
        
        // Failure
        $removeTaskPreReqDuplicate = TaskDao::removeTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("0", $removeTaskPreReqDuplicate);  
        
    }
    
    public function testGetTaskPreReqs()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        // Failure
        $taskPreReqsFailure = TaskDao::getTaskPreReqs(999);
        $this->assertNull($taskPreReqsFailure);
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2021-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2022-03-29 16:30:00", 22222, null, TaskTypeEnum::PROOFREADING);    
        $task3 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 3", "Task 3 Comment", "2023-03-29 16:30:00", 33333, null, TaskTypeEnum::TRANSLATION);

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $proofReadingTask = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $proofReadingTask);
        $this->assertNotNull($proofReadingTask->getId());
        
        $translationTask2 = TaskDao::create($task3);        
        $this->assertInstanceOf("Task", $translationTask2);
        
        $addTaskPreReq = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask->getId());      
        $this->assertEquals("1", $addTaskPreReq);
        $addTaskPreReq2 = TaskDao::addTaskPreReq($proofReadingTask->getId(), $translationTask2->getId());      
        $this->assertEquals("1", $addTaskPreReq2);
        
        // Success
        $taskPreReqs = TaskDao::getTaskPreReqs($proofReadingTask->getId());
        $this->assertCount(2, $taskPreReqs);
        foreach($taskPreReqs as $task) {
            $this->assertInstanceOf("Task", $task);
        }
        
    }
    
    public function testGetLatestAvailableTasks()
    {
        UnitTestHelper::teardownDb();        
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        // Failure
        $emptylatestTasks = TaskDao::getLatestAvailableTasks();
        $this->assertNull($emptylatestTasks);
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2021-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2022-03-29 16:30:00", 22222, null, TaskTypeEnum::TRANSLATION);
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $translationTask2 = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $translationTask2);
        
        // Success
        $latestTasks = TaskDao::getLatestAvailableTasks();
        $this->assertCount(2, $latestTasks);
        foreach($latestTasks as $task) {
            $this->assertInstanceOf("Task", $task);
        }
    }
    
    public function testGetUserTopTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2021-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2022-03-29 16:30:00", 22222, null, TaskTypeEnum::TRANSLATION);
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $translationTask2 = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $translationTask2);
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Success
        $userTopTasks = TaskDao::getUserTopTasks($insertedUser->getId(), false, 30);
        $this->assertCount(2, $userTopTasks);
        foreach($userTopTasks as $task) {
            $this->assertInstanceOf("Task", $task);
        }
    }
    
    public function testArchiveTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Success
        $archiveTask = TaskDao::archiveTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $archiveTask);
        
        // Failure
        $archiveTaskFailure = TaskDao::archiveTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("0", $archiveTaskFailure);
  
    }
    
    public function testClaimTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Success
        $claimTask = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Failure
        $claimTaskFailure = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("0", $claimTaskFailure);        
    }
    
    public function testUnClaimTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());

        $claimTask = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $unClaimTask = TaskDao::unClaimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $unClaimTask);
        
        // Success
        $unClaimTaskFailure = TaskDao::unClaimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("0", $unClaimTaskFailure);
    }
    
    public function hasUserClaimedTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Failure
        $hasUserClaimedTaskFailure = TaskDao::hasUserClaimedTask($insertedUser->getId(), $translationTask->getId());
        $this->assertEquals("0", $hasUserClaimedTaskFailure);  

        $claimTask = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $hasUserClaimedTask = TaskDao::hasUserClaimedTask($insertedUser->getId(), $translationTask->getId());
        $this->assertEquals("1", $hasUserClaimedTask);
    }
    
    public function testTaskIsClaimed()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Failure
        $taskIsNotClaimed = TaskDao::taskIsClaimed($translationTask->getId());
        $this->assertEquals("0", $taskIsNotClaimed);

        $claimTask = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $taskIsClaimed = TaskDao::taskIsClaimed($translationTask->getId());
        $this->assertEquals("1", $taskIsClaimed);
    }
    
    public function testGetUserClaimedTask()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());   
        
        $task = UnitTestHelper::createTask($insertedProject->getId());
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());
        
        // Failure
        $noTaskTranslator = TaskDao::getUserClaimedTask($translationTask->getId());
        $this->assertNull($noTaskTranslator);

        $claimTask = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        // Success
        $taskTranslator = TaskDao::getUserClaimedTask($translationTask->getId());
        $this->assertInstanceOf("User", $taskTranslator);
        $this->assertNotNull($taskTranslator->getId());
        $this->assertEquals($insertedUser->getId(), $taskTranslator->getId());
    }
    
    public function testGetUserTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());  
        
        // Failure        
        $userTasksFailure = TaskDao::getUserTasks($insertedUser->getId());
        $this->assertNull($userTasksFailure);  
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2021-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2022-03-29 16:30:00", 22222, null, TaskTypeEnum::TRANSLATION);
        
        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $translationTask2 = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $translationTask2);
        
        $claimTask = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        $claimTask2 = TaskDao::claimTask($translationTask2->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask2);
        
        // Success        
        $userTasks = TaskDao::getUserTasks($insertedUser->getId());
        $this->assertCount(2, $userTasks);
        foreach($userTasks as $task) {
            $this->assertInstanceOf("Task", $task);
        }
    }
    
    public function testGetUserArchivedTasks()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());  
        
        // Failure        
        $userArchivedTasksFailure = TaskDao::getUserArchivedTasks($insertedUser->getId());
        $this->assertNull($userArchivedTasksFailure);  
        
        $task = UnitTestHelper::createTask($insertedProject->getId());

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        
        $claimTask = TaskDao::claimTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $claimTask);
        
        $archivedTask = TaskDao::archiveTask($translationTask->getId(), $insertedUser->getId());
        $this->assertEquals("1", $archivedTask);
        
        // Success
        $userArchivedTasks = TaskDao::getUserArchivedTasks($insertedUser->getId());
        $this->assertCount(1, $userArchivedTasks);
        $this->assertInstanceOf("ArchivedTask", $userArchivedTasks[0]);
        $this->assertEquals($translationTask->getProjectId(), $userArchivedTasks[0]->getProjectId());
        $this->assertEquals($translationTask->getTitle(), $userArchivedTasks[0]->getTitle());
        $this->assertEquals($translationTask->getComment(), $userArchivedTasks[0]->getComment());
        $this->assertEquals($translationTask->getDeadline(), $userArchivedTasks[0]->getDeadline());

//        $this->assertEquals($translationTask->getSourceLocale()->getLanguageCode(), $userArchivedTasks[0]->getSourceLocale()->getLanguageCode());
//        $this->assertEquals($translationTask->getSourceLocale()->getCountryCode(), $userArchivedTasks[0]->getSourceLocale()->getCountryCode());
//        $this->assertEquals($translationTask->getTargetLocale()->getLanguageCode(), $userArchivedTasks[0]->getTargetLocale()->getLanguageCode());
//        $this->assertEquals($translationTask->getTargetLocale()->getCountryCode(), $userArchivedTasks[0]->getTargetLocale()->getCountryCode());
//        
        $this->assertEquals($translationTask->getTaskType(), $userArchivedTasks[0]->getTaskType());
        $this->assertEquals(3, $userArchivedTasks[0]->getTaskStatus()); // Claimed the task, so status changes
        $this->assertEquals($translationTask->getPublished(), $userArchivedTasks[0]->getPublished());        
        $this->assertNotNull($userArchivedTasks[0]->getArchiveUserId());
        $this->assertNotNull($userArchivedTasks[0]->getArchiveDate());
    }
    
    public function testGetTasksWithTag()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());    
        
        // Failure
        $getTasksWithTagFailure = TaskDao::getTasksWithTag(999);
        $this->assertNull($getTasksWithTagFailure);
        
        $task = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 1", "Task 1 Comment", "2022-03-29 16:30:00", 11111, null, TaskTypeEnum::TRANSLATION);
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment", "2021-03-29 16:30:00", 22222, null, TaskTypeEnum::TRANSLATION);        

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask);
        
        $translationTask2 = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $translationTask2);
        
        $tag = TagsDao::getTag(null, "Tags");
        $tag = $tag[0];
        
        // Success          
//        $getTasksWithTag = TaskDao::getTasksWithTag($tag->getId());
//        $this->assertCount(2, $getTasksWithTag);
//        foreach($getTasksWithTag as $task) {
//            $this->assertInstanceOf("Task", $task);
//        }        
    }  
    
    public function testCheckTaskFileVersion()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $task = UnitTestHelper::createTask($insertedProject->getId());

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        // Success
        $checkTaskFileVersion = TaskDao::checkTaskFileVersion($translationTask->getId());
        $this->assertEquals(false, $checkTaskFileVersion);   
    }
    
    public function testRecordFileUpload()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getId());  
        
        $task = UnitTestHelper::createTask($insertedProject->getId());

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());  
        
        // Success
        $recordFileUpload = TaskDao::recordFileUpload($translationTask->getId(), "examplefile", "text/plain", $insertedUser->getId());
        $this->assertNotNull($recordFileUpload);
    }
    
    public function testGetLatestFileVersion()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        $this->assertNotNull($insertedProject->getId());
        
        $task = UnitTestHelper::createTask($insertedProject->getId());

        $translationTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $translationTask); 
        $this->assertNotNull($translationTask->getId());
        
        // Success
        $latestFileVersion = TaskDao::getLatestFileVersion($translationTask->getId());
        $this->assertEquals(0, $latestFileVersion);   
    }
    
}
?>
