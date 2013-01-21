{include file="header.tpl"}

{assign var="project_id" value=11} {* $project->getId()} *}
<h1 class="page-header">
    Project Title{* {$project->getTitle()} *}
    <small>Alter project details here.</small>
    <a href="{urlFor name="project-view" options="project_id.$project_id"}" class='pull-right btn btn-primary'>
        <i class="icon-list icon-white"></i> View Details
    </a>
</h1>

<h3>Edit Project Details</h3>
<p style="margin-bottom:20px;"></p>
<form method="post" action="{urlFor name="project-alter" options="project_id.$project_id"}" class="well">
    <label for="title">Title:</label>
    <textarea wrap="soft" cols="1" rows="2" name="title">asd</textarea> {*$project->getTitle()*}

    <label for="description">Description:</label>
    <textarea wrap="soft" cols="1" rows="6" name="description">asf</textarea> {*$project->getDescription()*}
    
    <label for="deadline">Deadline:</label>
    <textarea wrap="soft" cols="1" rows="2" name="deadline">asf</textarea> {*$project->getDeadline()*}    
    
    <label for="reference">Reference:</label>
    <textarea wrap="soft" cols="1" rows="3" name="reference">asf</textarea> {*$project->getReference()*}    
    
    <label for="word_count">Word Count:</label>
    <input type="text" name="word_count" id="word_count" maxlength="6" value="999"> {*$project->getWordCount()*}
    <p>
        <button type="submit" value="Submit" name="submit" class="btn btn-primary">
            <i class="icon-refresh icon-white"></i> Update
        </button>
    </p>
</form>


{include file="footer.tpl"}