{include file="header.tpl"}

    <h1 class="page-header">
        <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block; word-break:break-all;">
            {$project->getTitle()}
            <small>Overview of project details.</small>
        </span>
        {assign var="project_id" value=$project->getId()}

        {if isset($isOrgMember)}
            <div class="pull-right">
                <a href="{urlFor name="project-alter" options="project_id.$project_id"}" class='pull-right btn btn-primary'>
                    <i class="icon-wrench icon-white"></i> Edit Project
                </a> 
            </div>
        {/if}
    </h1>

{if isset($flash['success'])}
    <p class="alert alert-success">
        {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        {$flash['error']}
    </p>
{/if}

    <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all; table-layout: fixed;">
        <thead>            
            <th style="text-align: left;"><strong>Organisation</strong></th>
            <th>Source Language</th>
            <th>Reference</th>
            <th>Word Count</th>
            <th>Created</th>
            <th>Project Deadline</th>
            {if isset($userSubscribedToProject)}
                <th>Tracked</th>
            {/if}

        </thead>
        <tbody>
            <tr style="overflow-wrap: break-word; word-break:break-all;">
                <td style="text-align: left; overflow-wrap: break-word; word-break:break-all;">
                    {if isset($org)}
                        {assign var="org_id" value=$org->getId()}
                        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a>
                    {/if}
                </td>
                <td>
                    {TemplateHelper::getTaskSourceLanguage($project)}
                </td>
                <td>
                    {if $project->getReference() != ''}
                        <a target="_blank" href="{$project->getReference()}">{$project->getReference()}</a>
                    {else}
                        -
                    {/if}
                </td>
                <td>
                    {if $project->getWordCount() != ''}
                        {$project->getWordCount()}
                    {else}
                        -
                    {/if}
                </td>
                <td>
                    {date(Settings::get("ui.date_format"), strtotime($project->getCreatedTime()))}
                </td>  
                <td>
                    {date(Settings::get("ui.date_format"), strtotime($project->getDeadline()))}
                </td>
                {if isset($userSubscribedToProject)}
                    <td>

                        <form id="trackedProjectForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                             {if $userSubscribedToProject}
                                <p>
                                    <input type="hidden" name="trackProject" value="0" />
                                    <a class="btn btn-small" onclick="$('#trackedProjectForm').submit();" >
                                        <i class="icon-check icon-black"></i> Tracked
                                    </a>
                                </p>
                            {else}
                                <p>
                                    <input type="hidden" name="trackProject" value="1" />
                                    <a class="btn btn-small btn-inverse" onclick="$('#trackedProjectForm').submit();" >
                                        <i class="icon-envelope icon-white"></i> Untracked
                                    </a>
                                </p>
                            {/if}
                        </form>                     
                    </td>
                {/if}
            </tr>
            <tr>
            </tr> 
        </tbody>
    </table>            
            
    <div class="well">
        <table border="0" width="100%" style="overflow-wrap: break-word; word-break:break-all; table-layout: fixed;">
            <thead>
            <th align="left" width="48%">Description:<hr/></th>
            <th></th>
            <th align="left" width="48%">Impact:<hr/></th>
            </thead>
            <tbody>
                <tr valign="top">
                    <td>
                        <i>
                        {if $project->getDescription() != ''}
                            {$project->getDescription()}
                        {else}
                            No description has been added.
                        {/if}  
                        </i>
                    </td>
                    <td></td>
                    <td>
                        <i>
                        {if $project->getImpact() != ''}
                            {$project->getImpact()}
                        {else}
                            No impact has been added.
                        {/if}  
                        </i>               
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-bottom: 40px"></td>
                </tr>
                <tr valign="top">
                    <td colspan="3">
                        <strong>Tags:</strong><hr/>
                    </td>
                </tr>
                <tr>                
                    <td class="nav nav-list unstyled" style="padding-left: 0px; padding-right: 0px;" colspan="2">
                    {if isset($project_tags) && is_array($project_tags)}
                        {foreach $project_tags as $ptag}
                            {assign var="tag_label" value=$ptag->getLabel()}
                            {assign var="tagId" value=$ptag->getId()}
                            <a class="tag label" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
                        {/foreach}
                    {else}
                        <i>There are no tags associated with this project.</i>                    
                    {/if}
                    </td>                
                </tr>
            </tbody>
        </table>
    </div>            
                
    <p style="margin-bottom:40px;"/>

{if isset($user) && ($isOrgMember || $isAdmin)}
    <hr />    
    <h1 class="page-header" style="margin-bottom: 60px">
        Tasks
        <small>Overview of tasks created for this project.</small>

        <a class="pull-right btn btn-success" href="{urlFor name="task-create" options="project_id.$project_id"}">
            <i class="icon-upload icon-white"></i> Create Task
        </a> 
    </h1> 
            
    {if isset($flash['taskSuccess'])}
        <div class="alert alert-success">
            {$flash['taskSuccess']}
        </div>
    {else if isset($flash['taskError'])}
        <div class="alert alert-error">
            {$flash['taskError']}
        </div>
    {/if}        

    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">List View</a></li>
            <li><a href="#tabs-2">Graph View</a></li>
        </ul>
        <div id="tabs-1">
            {if isset($projectTasks) && count($projectTasks) > 0}
                {foreach from=$taskLanguageMap key=languageCountry item=tasks}           

                    <div style="display: inline-block; overflow-wrap: break-word; word-break:break-all; 
                                    font-weight: bold; font-size: large; max-width: 70%">
                        {TemplateHelper::getLanguageAndCountryFromCode($languageCountry)}
                    </div>                
                    <hr />  
 
                    <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all; margin-bottom: 60px">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>       
                                <th>Type</th> 
                                <th>Task Deadline</th>                  
                                <th>Published</th>                    
                                <th>Tracked</th>
                                <th>Edit</th>
                                <th>Archive/Delete</th>
                            </tr>
                        </thead>
                        <tbody>

                            {foreach from=$tasks item=task}
                                {assign var="task_id" value=$task->getId()}
                                <tr style="overflow-wrap: break-word; word-break:break-all;">
                                    <td width="24%">
                                        <a href="{urlFor name="task-view" options="task_id.$task_id"}">
                                            {$task->getTitle()}
                                        </a>
                                        <br/>
                                    </td>
                                    <td>
                                        {assign var="status_id" value=$task->getTaskStatus()}
                                        {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                            Waiting
                                        {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                            Unclaimed
                                        {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                            <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">
                                                In Progress
                                            </a>
                                        {elseif $status_id == TaskStatusEnum::COMPLETE}
                                            {assign var="org_id" value=$project->getOrganisationId()}
                                            <a href="{urlFor name="org-task-review" options="task_id.$task_id|org_id.$org_id"}">
                                                Complete
                                            </a>
                                        {/if}
                                    </td>
                                    <td>
                                        <strong>
                                            <small>                                  
                                                {assign var="type_id" value=$task->getTaskType()}
                                                {if $type_id == TaskTypeEnum::SEGMENTATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">
                                                        Segmentation
                                                    </span>                                    
                                                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">
                                                        Translation
                                                    </span> 
                                                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">
                                                        Proofreading
                                                    </span> 
                                                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">
                                                        Desegmentation
                                                    </span> 
                                                {/if}
                                            </small>
                                        </strong>
                                    </td>
                                    <td>
                                        {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}
                                    </td>
                                    <td>
                                        <form id="publishedForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" style="text-align: center">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $task->getPublished() == 1}
                                                <a class="btn btn-small" onclick="$('#publishedForm{$task_id}').submit();" >
                                                    <i class="icon-check icon-black"></i> Published
                                                </a>                                                
                                                <input type="hidden" name="publishedTask" value="0" />
                                            {else}                                        
                                                <input type="hidden" name="publishedTask" value="1" />
                                                <a class="btn btn-small btn-inverse" onclick="$('#publishedForm{$task_id}').submit();" >
                                                    <i class="icon-remove-circle icon-white"></i> Unpublished
                                                </a>
                                            {/if}
                                        </form>

                                    </td>
                                    <td>
                                        <form id="trackedForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $taskMetaData[$task_id]['tracking']}
                                                <input type="hidden" name="trackTask" value="0" />
                                                <a class="btn btn-small" onclick="$('#trackedForm{$task_id}').submit();" >
                                                    <i class="icon-inbox icon-black"></i> Tracked
                                                </a>
                                            {else}
                                                <input type="hidden" name="trackTask" value="1" />
                                                <a class="btn btn-small btn-inverse" onclick="$('#trackedForm{$task_id}').submit();" >
                                                    <i class="icon-envelope icon-white"></i> Untracked
                                                </a>
                                            {/if}
                                        </form>
                                    </td>    
                                    <td>
                                        <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class="btn btn-small">
                                            <i class="icon-wrench icon-black"></i> Edit Task
                                        </a>
                                    </td>
                                    <td>
                                        <form id="archiveDeleteForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $status_id < TaskStatusEnum::IN_PROGRESS}
                                                <input type="hidden" name="deleteTask" value="Delete" />
                                                <a class="btn btn-small btn-inverse" onclick="$('#trackedForm{$task_id}').submit();" >
                                                    <i class="icon-fire icon-white"></i> Delete
                                                </a> 
                                            {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                                <button class="btn btn-small btn-inverse" disabled>
                                                    <i class="icon-fire icon-white"></i> Cannot archive in progress tasks.
                                                </button>  
                                            {else}
                                                <input type="hidden" name="archiveTask" value="Delete" />
                                                <a class="btn btn-small btn-inverse" onclick="$('#trackedForm{$task_id}').submit();" >
                                                    <i class="icon-fire icon-white"></i> Archive
                                                </a> 
                                            {/if}
                                        </form>
                                    </td>
                                </tr>                        
                            {/foreach}
                        </tbody>
                    </table>        
                {/foreach}
            {else}
                <div class="alert alert-warning">
                    <strong>What now?</strong> You don't have any tasks uploaded for your organisation. 
                    If you have content to be translated, please add a new task for that content.
                </div>
            {/if}
        </div>
        <div id="tabs-2">
            {$graph}
        </div>
    </div>
        
{else}
    {if isset($projectTasks)}
    <p class="alert alert-info">
        Please log in to register for notifications for this project.
    </p>
    {/if}
{/if}

{include file="footer.tpl"}
