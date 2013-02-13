{assign var=task_id value=$task->getId()}

<section>
    <div class="page-header">
        <h1>{$task->getTitle()} <small>Chunking task</small></h1>
    </div>
</section>

<section>
    <h2>Do you want to chunk this file? <small>After downloading</small></h2>
    <hr />
    <h3>Review this checklist for your downloaded file <small>Will you be able to chunk this file?</small></h3>
    <p style="margin-bottom:20px;"></p>
    <ol>
        <li>Can you <strong>open the file</strong> on your computer?</li>
        <li><strong>Will you have enough time to chunk</strong> this file? Check how long the file is.</li>
    </ol>
</section>

<section>
     <h3>It&rsquo;s time to decide</h3>
     <p> 
         Do you want to chunk this file? When you are finished chunking the file, you will need to upload a file for each chunk.
     </p>
     <p> 
        <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
             <button type="submit" class="btn btn-primary">
                 <i class="icon-ok-circle icon-white"></i> Yes, I promise I will chunk this file
             </button>
             <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                 <i class="icon-ban-circle icon-black"></i> No, just bring me back to the task page
             </a>
        </form>
    </p>
</section>

<iframe src="{urlFor name="download-task" options="task_id.$task_id"}?convertToXliff={$convert}" width="1" height="1" frameborder="no"></iframe>