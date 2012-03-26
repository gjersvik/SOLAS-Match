{include file="header.tpl"}
<div class="page-header">
	<h1>{$task->getTitle()} <small>Translation task</small></h1>
</div>

{assign var="task_id" value=$task->getTaskId()}
{if isset($user)}
	<p>
		<a href="{urlFor name="download-task" options="task_id.$task_id"}" class="btn btn-large btn-primary">Download and translate</a>
	</p>
{/if}
<p>
	{if $task->getSourceId()}
		From {Languages::languageNameFromId($task->getSourceId())}
	{/if}
	{if $task->getTargetId()}
		To {Languages::languageNameFromId($task->getTargetId())}
	{/if}
	{foreach from=$task->getTags() item=tag}
		<a class="tag" href="{URL::tag($tag)}"><span class="label">{$tag}</span></a>
	{/foreach}
</p>
	
<p>
	<span class="time_since">{IO::timeSinceSqlTime($task->getCreatedTime())} ago</span>
	&middot; {Organisations::nameFromId($task->getOrganisationId())}
	{assign var="wordcount" value=$task->getWordCount()}
	{if $wordcount}
		&middot; {$wordcount|number_format} words
	{/if}
</p>

{if isset($task_file_info)}
	{assign var="task_id" value=$task->getTaskId()}
	{if isset($user)}
		<hr>
		<h2>Finished translating? <small>Upload your changes</small></h2>
		<form class="well" method="post" action="{urlFor name="task-upload-edited" options="task_id.$task_id"}" enctype="multipart/form-data">
			<input type="hidden" name="task_id" value="{$task->getTaskId()}">
			<input type="file" name="edited_file" id="edited_file">
			<p class="help-block">
				Max file size {$max_file_size}MB.
			</p> 
			<button type="submit" value="Submit" name="submit" class="btn">Submit</button>
		</form>
	{else}
		<p>Please <a href="{urlFor name="login"}">log in</a> to be able to accept translation jobs.</p>
	{/if}
{/if}

{include file="footer.tpl"}
