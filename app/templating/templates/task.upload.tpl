{include file="header.inc.tpl"}
<div class="grid_8">
	<h2>Upload a document to be translated</h2>
	{if isset($error)}
		<p class="error">{$error}</p>
	{/if}
	<form method="post" action="{$url_task_upload}" enctype="multipart/form-data">
		<fieldset>
			<p><label for="{$field_name}">Choose your file</label>  
			<input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size_bytes}">
			<input type="file" name="{$field_name}" id="{$field_name}"></p>
			<p class="desc">Can be anything, even a .zip collection of files. Max file size {$max_file_size_mb}MB.</p>
			
			<input type="hidden" name="organisation_id" value="1">
			<input type="submit" value="Upload my selected file" name="submit">
		</fieldset> 
	</form>
</div>
{include file="footer.inc.tpl"}