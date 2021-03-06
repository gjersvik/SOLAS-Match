<?php

require_once __DIR__."/../../Common/lib/WorkflowBuilder.class.php";

class APIWorkflowBuilder extends WorkflowBuilder
{
    protected function getProjectTasks($projectId)
    {
        $dao = new TaskDao();
        return $dao->getTask(null, $projectId);
    }

    protected function getTaskPreReqs($taskId)
    {
        $dao = new TaskDao();
        return $dao->getTaskPreReqs($taskId);
    }
}
