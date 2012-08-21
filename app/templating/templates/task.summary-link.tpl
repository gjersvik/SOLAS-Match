{* Must have an object $task assigned by parent *}
<div class="task">
    {assign var='task_id' value=$task->getTaskId()}
        <h2><a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a></h2>
        <p>
        	{if $task->getSourceId()}
        		From {Languages::languageNameFromId($task->getSourceId())}
        	{/if}
        	{if $task->getTargetId()}
        		To {Languages::languageNameFromId($task->getTargetId())}
        	{/if}

		    {foreach from=$task->getTags() item=tag}
	    		<a href="{urlFor name="tag-details" options="label.$tag"}" class="label"><span class="label">{$tag}</span></a>
     		{/foreach}
    	</p>

    {if isset($current_page) AND $current_page == 'user-profile'}
        {if $task->getStatus()}
            <p><span class="label label-info">{$task->getStatus()}</span></p>
        {/if}
    {/if}
	
	<p class="task_details">
		Added {IO::timeSinceSqlTime($task->getCreatedTime())} ago
		&middot; By 
        {assign var="org_id" value=$task->getOrganisationId()}
        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            {OrganisationDao::nameFromId($task->getOrganisationId())}
        </a>
		{if $task->getWordcount()}
			&middot; {$task->getWordcount()|number_format} words
		{/if}
	</p>
</div>
