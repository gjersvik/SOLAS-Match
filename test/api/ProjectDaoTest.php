<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/OrganisationDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/ProjectDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/UserDao.class.php';
require_once __DIR__.'/../../api/DataAccessObjects/TaskDao.class.php';
require_once __DIR__.'/../../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/../UnitTestHelper.php';



class ProjectDaoTest extends PHPUnit_Framework_TestCase
{
    public function testProjectCreate()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
                
        $project = UnitTestHelper::createProject($insertedOrg->getId());
        
        // Success
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);        
        $this->assertNotNull($insertedProject->getId());        
        $this->assertEquals($project->getTitle(), $insertedProject->getTitle());
        $this->assertEquals($project->getDescription(), $insertedProject->getDescription());
        $this->assertEquals($project->getDeadline(), $insertedProject->getDeadline());
        $this->assertEquals($project->getImpact(), $insertedProject->getImpact());
        $this->assertEquals($project->getReference(), $insertedProject->getReference());
        $this->assertEquals($project->getWordCount(), $insertedProject->getWordCount());
        $this->assertEquals($project->getSourceCountryCode(), $insertedProject->getSourceCountryCode());
        $this->assertEquals($project->getSourceLanguageCode(), $insertedProject->getSourceLanguageCode());
        
        $projectTags = $insertedProject->getTag();
        $this->assertCount(2, $projectTags);
        foreach($projectTags as $tag) {
            $this->assertInstanceOf("Tag", $tag);
        }
        
        $this->assertEquals($project->getOrganisationId(), $insertedProject->getOrganisationId());
        $this->assertNotNull($insertedProject->getCreatedTime());    

    }
    
    public function testProjectUpdate()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        $this->assertNotNull($insertedOrg->getId());
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);          
        
        $org2 = UnitTestHelper::createOrg(NULL, "Organisation 2", "Organisation 2 Bio", "http://www.organisation2.org");
        $insertedOrg2 = OrganisationDao::insertAndUpdate($org2);
        $this->assertInstanceOf("Organisation", $insertedOrg2);
        $this->assertNotNull($insertedOrg2->getId());
        
        $insertedProject->setTitle("Updated Title");
        $insertedProject->setDescription("Updated Description");
        $insertedProject->setDeadline("2030-03-10 00:00:00");
        $insertedProject->setImpact("Updated Impact");
        $insertedProject->setReference("Updated Reference");
        $insertedProject->setWordCount(654321);
        $insertedProject->setSourceCountryCode("AZ");
        $insertedProject->setSourceLanguageCode("agx");
        $insertedProject->setTag(array("Updated Project", "Updated Tags"));
        $insertedProject->setOrganisationId($insertedOrg2->getId());
        $insertedProject->setCreatedTime("2030-06-20 00:00:00");
  
        // Success
        $updatedProject = ProjectDao::createUpdate($insertedProject);
        $this->assertInstanceOf("Project", $updatedProject);
        $this->assertEquals($insertedProject->getTitle(), $updatedProject->getTitle());
        $this->assertEquals($insertedProject->getDescription(), $updatedProject->getDescription());
        $this->assertEquals($insertedProject->getDeadline(), $updatedProject->getDeadline());
        $this->assertEquals($insertedProject->getImpact(), $updatedProject->getImpact());
        $this->assertEquals($insertedProject->getReference(), $updatedProject->getReference());
        $this->assertEquals($insertedProject->getWordCount(), $updatedProject->getWordCount());
        $this->assertEquals($insertedProject->getSourceCountryCode(), $updatedProject->getSourceCountryCode());
        $this->assertEquals($insertedProject->getSourceLanguageCode(), $updatedProject->getSourceLanguageCode());

        $projectTagsAfterUpdate = $updatedProject->getTag();
        $this->assertCount(2, $projectTagsAfterUpdate);
        foreach($projectTagsAfterUpdate as $tag) {
            $this->assertInstanceOf("Tag", $tag);
        }       
        $this->assertEquals("Updated Project", $projectTagsAfterUpdate[0]->getLabel());
        $this->assertEquals("Updated Tags", $projectTagsAfterUpdate[1]->getLabel());
        
        $this->assertEquals($insertedProject->getOrganisationId(), $updatedProject->getOrganisationId());
        $this->assertEquals($insertedProject->getCreatedTime(), $updatedProject->getCreatedTime()); 
        
    }
    
    public function testGetProject()
    {
        UnitTestHelper::teardownDb();

        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject); 
        
        $paramsSuccess = array(
            "id"                => $insertedProject->getId(),
            "title"             => $project->getTitle(),
            "description"       => $project->getDescription(),
            "impact"            => $project->getImpact(),
            "deadline"          => $project->getDeadline(),
            "organisation_id"   => $project->getOrganisationId(),
            "reference"         => $project->getReference(),
            "word-count"        => $project->getWordCount(),
            "created"           => $insertedProject->getCreatedTime(),
            "language_id"       => $project->getSourceLanguageCode(),
            "country_id"        => $project->getSourceCountryCode()            
        );
        
        // Success
        $resultGetProject = ProjectDao::getProject($paramsSuccess);
        $this->assertCount(1, $resultGetProject);
        $this->assertInstanceOf("Project", $resultGetProject[0]);        
        
        $paramsFail = array(
            "id" => 99           
        );
        
        // Failure
        $resultGetProjectFailure = ProjectDao::getProject($paramsFail);
        $this->assertNull($resultGetProjectFailure);
    }
    
    public function testArchiveProject()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);         
    
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        
        // Success
        $resultArchiveProject = ProjectDao::archiveProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertInstanceOf("ArchivedProject", $resultArchiveProject);
                
        // Failure        
        $resultArchiveProjectFailure = ProjectDao::archiveProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertNull($resultArchiveProjectFailure);
    }
    
    
    public function testGetArchivedProject()
    {
        UnitTestHelper::teardownDb();
        
        $org = UnitTestHelper::createOrg();
        $insertedOrg = OrganisationDao::insertAndUpdate($org);
        $this->assertInstanceOf("Organisation", $insertedOrg);
        
        $project = UnitTestHelper::createProject($insertedOrg->getId());        
        $insertedProject = ProjectDao::createUpdate($project);
        $this->assertInstanceOf("Project", $insertedProject);         
    
        $user = UnitTestHelper::createUser();
        $insertedUser = UserDao::save($user);
        $this->assertInstanceOf("User", $insertedUser);
        $this->assertNotNull($insertedUser->getUserId());

        $resultArchiveProject = ProjectDao::archiveProject($insertedProject->getId(), $insertedUser->getUserId());
        $this->assertInstanceOf("ArchivedProject", $resultArchiveProject);
        
        $paramsSuccess = array(
            "id"                => $insertedProject->getId(),
            "title"             => $insertedProject->getTitle(),
            "description"       => $insertedProject->getDescription(),
            "impact"            => $insertedProject->getImpact(),
            "deadline"          => $insertedProject->getDeadline(),
            "organisation_id"   => $insertedProject->getOrganisationId(),
            "reference"         => $insertedProject->getReference(),
            "word-count"        => $insertedProject->getWordCount(),
            "language_id"       => $insertedProject->getSourceLanguageCode(),
            "country_id"        => $insertedProject->getSourceCountryCode(),
            "created"           => $insertedProject->getCreatedTime(),
            "archived-date"     => $resultArchiveProject->getArchivedDate(),
            "user_id-archived"  => $resultArchiveProject->getTranslatorId()
        );
        
        // Success
        $resultGetArchivedProject = ProjectDao::getArchivedProject($paramsSuccess);
        $this->assertInstanceOf("ArchivedProject", $resultGetArchivedProject);
        $this->assertEquals($insertedProject->getTitle(), $resultGetArchivedProject->getTitle());
        $this->assertEquals($insertedProject->getDescription(), $resultGetArchivedProject->getDescription());
        $this->assertEquals($insertedProject->getDeadline(), $resultGetArchivedProject->getDeadline());
        $this->assertEquals($insertedProject->getImpact(), $resultGetArchivedProject->getImpact());
        $this->assertEquals($insertedProject->getReference(), $resultGetArchivedProject->getReference());
        $this->assertEquals($insertedProject->getWordCount(), $resultGetArchivedProject->getWordCount());
        $this->assertEquals($insertedProject->getSourceCountryCode(), $resultGetArchivedProject->getCountryCode());
        $this->assertEquals($insertedProject->getSourceLanguageCode(), $resultGetArchivedProject->getLanguageCode());
        $this->assertNotNull($resultGetArchivedProject->getArchivedDate());
        $this->assertNotNull($resultGetArchivedProject->getTranslatorId());
        
        $paramsFail = array(
            "id" => 999
        );
        
        // Failure
        $resultGetArchivedProjectFailure = ProjectDao::getArchivedProject($paramsFail);
        $this->assertNull($resultGetArchivedProjectFailure);
    }
    
    public function testGetProjectTasks()
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
        $task2 = UnitTestHelper::createTask($insertedProject->getId(), null, "Task 2", "Task 2 Comment");        

        $insertedTask = TaskDao::create($task);
        $this->assertInstanceOf("Task", $insertedTask);
        
        $insertedTask2 = TaskDao::create($task2);
        $this->assertInstanceOf("Task", $insertedTask2);
        
        // Success
        $resultGetProjectTasks = ProjectDao::getProjectTasks($insertedProject->getId());
        $this->assertCount(2, $resultGetProjectTasks);
        foreach($resultGetProjectTasks as $task) {
            $this->assertInstanceOf("Task", $task);
        }
        
        // Failure
        $resultGetProjectTasksFailure = ProjectDao::getProjectTasks(999);
        $this->assertNull($resultGetProjectTasksFailure);
    }
    
    public function testRecordProjectFileInfo()
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
        $this->assertNotNull($insertedUser->getUserId());
        
        // Success
        $resultRecordProjectFileInfo = ProjectDao::recordProjectFileInfo($insertedProject->getId(), "saveProjectFileTest.txt", $insertedUser->getUserId(), "text/plain");
        $this->assertNotNull($resultRecordProjectFileInfo);
        
        // Failure
        $resultRecordProjectFileInfoFailure = ProjectDao::recordProjectFileInfo($insertedProject->getId(), "saveProjectFileTest.txt", $insertedUser->getUserId(), "text/plain");
        $this->assertNull($resultRecordProjectFileInfoFailure);
    }    
    
    public function testGetProjectFileInfo()
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
        $this->assertNotNull($insertedUser->getUserId());
        
        $resultRecordProjectFileInfo = ProjectDao::recordProjectFileInfo($insertedProject->getId(), "saveProjectFileTest.txt", $insertedUser->getUserId(), "text/plain");
        $this->assertNotNull($resultRecordProjectFileInfo);
        
        // Success
        $resultGetProjectFileInfoSuccess = ProjectDao::getProjectFileInfo($insertedProject->getId(), $insertedUser->getUserId(), "saveProjectFileTest.txt", "saveProjectFileTest.txt", "text/plain");
        $this->assertInstanceOf("ProjectFile", $resultGetProjectFileInfoSuccess);
        
        // Failure
        $resultGetProjectFileInfoFailure = ProjectDao::getProjectFileInfo(999, $insertedUser->getUserId(), "saveProjectFileTest.txt", "saveProjectFileTest.txt", "text/plain");
        $this->assertNull($resultGetProjectFileInfoFailure);
    }
}
?>
