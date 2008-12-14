<div class="monbloc">
{if $project}
    <h2>Project: {$project->name|eschtml}</h2>
    <div class="blockcontent">
        <ul>
            <li><a href="{jurl 'rarangi~packages:index', array('project'=>$project->name)}">Packages</a></li>
            <li><a href="{jurl 'rarangi~sources:index', array('project'=>$project->name)}">Browse source</a></li>
        </ul>
    </div>
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}
</div>
