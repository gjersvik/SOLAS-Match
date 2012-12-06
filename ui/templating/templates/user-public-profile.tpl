{include file='header.tpl'}

{if isset($this_user)}
    <div class="page-header"><h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($this_user->getEmail())))}?s=80&r=g" alt="" />
        {assign var="user_id" value=$this_user->getUserId()}
        {if $this_user->getDisplayName() != ''}
            {$this_user->getDisplayName()}
        {else}
            User Profile
        {/if}
        <small>View user details here</small>   
        {if isset($private_access)}
            <a href="{urlFor name="create-org"}" class="btn btn-success pull-right">
                <i class="icon-star icon-white"></i> Create Organisation
            </a>
        {/if} 
    </h1></div>
{else}
    <div class='page-header'><h1>User Profile <small>View user details here</small></h1></div>
{/if}

<h1>
    {if isset($private_access)}
        <a href='{urlFor name="user-private-profile"}' class='pull-right btn btn-primary'>
            <i class="icon-wrench icon-white"></i> Edit Details
        </a>
    {/if}
</h1>

<h3>Public display name:</h3>
<p>{$this_user->getDisplayName()}</p>
 
{if TemplateHelper::getNativeLanguage($this_user) != ''}
    <h3>Native Language: </h3>
    <p>{TemplateHelper::getNativeLanguage($this_user)}</p>
{/if}
 
{if $this_user->getBiography() != ''}
    <h3>Biography:</h3>
    <p>{$this_user->getBiography()}</p>
{/if}

<p style="margin-bottom:50px;"></p>
{if isset($badges)}
    {if count($badges) > 0}
        <div class='page-header'>
            <h1>Badges<small> A list of badges you have earned</small>
                <a href='{urlFor name="badge-list"}' class='pull-right btn btn-primary'>
                    <i class="icon-list icon-white"></i> List All Badges
                </a>
            </h1>
        </div>

        {foreach $badges as $badge }     
            
            {if !is_null($badge->getOwnerId())}
                {assign var="user_id" value=$this_user->getUserId()} 
                <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="pull-right">
                    <input type="hidden" name="badge_id" value="{$badge->getId()}" />
                    <input type="hidden" value="Remove" onClick="return confirmPost()" />
                    <a href="#" onclick="this.parentNode.submit()" class="pull-right btn btn-inverse">
                        <i class="icon-fire icon-white"></i> Remove Badge
                    </a> 
                </form>                    
                {assign var="org_id" value=$badge->getOwnerId()}
                <h3>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        {$orgList[$org_id]->getName()}
                    </a>: {$badge->getTitle()}           
                </h3>
                <p>{$badge->getDescription()}</p>    
            {else}
                <h3>SOLAS Badge: {$badge->getTitle()}</h3>            
                <p>{$badge->getDescription()}</p>                
            {/if}
            <p style="margin-bottom:20px;"></p>
        {/foreach}
        
        <p style="margin-bottom:50px;"></p>
    {/if}
{/if}

{if isset($user_tags)}
    {if count($user_tags) > 0}
        <div class="page-header">
            <h1>Tags<small> A list of tags you have subscribed to.</small>
                <a href='{urlFor name='tags-list'}' class="pull-right btn btn-primary">
                    <i class="icon-list icon-white"></i> List All Tags
                </a>
            </h1>
        </div>

        {foreach $user_tags as $tag}
            <p>
                {assign var="tag_label" value=$tag->getLabel()}
                <a class="tag" href="{urlFor name="tag-details" options="label.$tag_label"}">
                    <span class="label">{$tag_label}</span>
                </a>
            </p>
        {/foreach}
        <p style="margin-bottom:50px;"></p>
    {/if}
{/if}

{if isset($user_orgs)}
    {if count($user_orgs) > 0}
        <div class='page-header'>
            <h1>
                Organisations <small>A list of organisations you belong to</small>
                <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                    <i class="icon-search icon-white"></i> Search for Organisations
                </a>
            </h1>
        </div>

        {foreach $user_orgs as $org}
            <div class="row">
                {assign var="org_id" value=$org->getId()}
                {assign var="user_id" value=$this_user->getUserId()}
                <div class="span8">
                    <h3>
                        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a>
                    </h3>
                </div>
                <div class="span4">
                    <form method="post" class="pull-right" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                        {if isset($private_access)}
                            <input type="hidden" name="org_id" value="{$org_id}" />
                            <input type="hidden" name="revoke" value="Leave Organisation" 
                                   onclick="return confirm('Are you sure you want to leave the organisation?')"/>
                                <a href="#" onclick="this.parentNode.submit()" class="pull-right btn btn-inverse">
                                    <i class="icon-fire icon-white"></i> Leave Organisation
                                </a>
                        {/if}
                    </form>
                </div>
                <div class="span8">
                    <p>
                        {if $org->getBiography() == ''}
                            This organisation does not have a biography listed.
                        {else}                            
                            {$org->getBiography()}
                        {/if}
                    </p>
                     
                    <p>
                    {if $org->getHomePage() != "http://"}
                        Visit their <a target="_blank" href="{$org->getHomePage()}">home page</a>.
                    {else}
                        This organisation does not have a web site listed.
                    {/if}
                    </p>
                </div>
            </div>
            <p style="margin-bottom:20px;"></p>
        {/foreach}
        <p style="margin-bottom:50px;"></p>
    {/if}
{/if}

{if isset($activeJobs)}
    {if count($activeJobs) > 0}
        <div class='page-header'><h1>Active Tasks <small>A list of tasks you are currently working on</small>
        {if isset($private_access)}
            <a href='{urlFor name="active-tasks" options="page_no.1"}' class='pull-right btn btn-primary'>
                <i class="icon-list icon-white"></i> List All Active Tasks
            </a>
        {/if}
        </h1></div>

        {foreach $activeJobs as $job}
                {include file="task.summary-link.tpl" task=$job}
        {/foreach}
        <p style="margin-bottom:50px;"></p>
    {/if}
{/if}

{if isset($archivedJobs)}
    {if count($archivedJobs) > 0}
        <div class='page-header'><h1>Archived Jobs <small>A list of jobs you have worked on in the past</small>
        {if isset($private_access)}
            <a href='{urlFor name="archived-tasks" options="page_no.1"}' class='pull-right btn btn-primary'>
                <i class="icon-list icon-white"></i> List All Archived Tasks
            </a>
        {/if}
        </h1></div>

        {foreach $archivedJobs as $job}
            {include file="task.profile-display.tpl" task=$job}
        {/foreach}
        <p style="margin-bottom:50px;"></p>
    {/if}
{/if}

{include file='footer.tpl'}