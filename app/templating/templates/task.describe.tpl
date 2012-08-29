{include file="header.tpl"}
	<div class="grid_8">
		<h2>Describe your task</h2>
		{if isset($error)}
			<div class="alert alert-error">
				{$error}
			</div>
		{/if}
		<form method="post" action="{$url_task_describe}">
			<fieldset>
				<label for="content">
                    Descriptive Title
                    {if !is_null($title_error)}
                        <div class="alert alert-error">
                            {$title_error}
                        </div>
                    {/if}
                </label>
				<textarea wrap="hard" cols="1" rows="2" name="title">{$task->getTitle()}</textarea>
				<p class="desc">You may replace the file name with something more descriptive.</p>

                <label for="impact">Task Impact</label>
                <p>Who and what will be affected by the translation of this task</p>
                <textarea wrap="hard" cols="1" rows="2" name="impact">{$task->getImpact()}</textarea>

                <label for="reference">Context Reference</label>
                <p class="desc">Enter a URL that gives context to this task</p>
                {if $task->getReferencePage() != '' }
                    {assign var="url_text" value=$task->getReferencePage()}
                {else}
                    {assign var="url_text" value="http://"}
                {/if}
                <textarea wrap="hard" cols="1" rows="2" name="reference">{$url_text}</textarea>

                {if isset($languages)}
                    <p>
                        <label for="source">From language</label>
                        <select name="source" id="source">
                            {foreach $languages as $language}
                                <option value="{$language[0]}">{$language[0]}</option>
                            {/foreach}
                        </select>
                        {if isset($countries)}
                            <select name="sourceCountry" id="sourceCountry">
                                {foreach $countries as $country}
                                    <option value="{$country[1]}">{$country[0]}</option>
                                {/foreach}
                            </select>
                        {/if}
                    </p>
                    <p>
                        <label for="target_0">To language</label>
                        <select name="target_0" id="target_0">
                            {foreach $languages as $language}
                                <option value="{$language[0]}">{$language[0]}</option>
                            {/foreach}
                        </select>
                        {if isset($countries)}
                            <select name="targetCountry_0" id="targetCountry">
                                {foreach $countries as $country}
                                    <option value="{$country[1]}">{$country[0]}</option>
                                {/foreach}
                            </select>
                        {/if}
                        <div id="text">
                        </div>
                        <input type="button" onclick="addInput()" value="Add More Targets"/>
                    </p>
                {else}
    				<p>
    					<label for="source">From language</label>
    					<input type="text" name="source" id="source">
                                        <input type="text" name="sourceCountry" id="source">
    				</p>
    				<p>
    					<label for="target">To language</label>
    					<input type="text" name="target" id="target">
                                        <input type="text" name="targetCountry" id="source">
    				</p>
                {/if}
				
				<p>
					<label for="tags">Tags</label>
					<input type="text" name="tags" id="tags">
				</p>
				<p class="desc">Separated by spaces. For multiword tags: join-with-hyphens</p>
				
				<p>
					<label for="word_count">
                        Word count
                        {if !is_null($word_count_err)}
                            <div class="alert alert-error">
                                {$word_count_err}
                            </div>
                        {/if}
                    </label>  
					<input type="text" name="word_count" id="word_count" maxlength="6">
				</p>
				<p class="desc">Approximate, or leave black.</p>  
				
				<button type="submit" value="Submit" name="submit" class="btn btn-primary"> Submit</button>
			</fieldset> 
		</form>
	</div>
{include file="footer.tpl"}
