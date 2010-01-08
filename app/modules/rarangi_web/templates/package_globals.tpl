{if $project}

    <h2>Globals variable of package {$package->name}</h2>

    <div class="block">
        {if $components !== null && $components->rowCount()}
            <ul>
            {foreach $components as $comp}
            <li><a href="{jurl 'rarangi_web~components:globaldetails', array('project'=>$project->name,'package'=>$package->name, 'globalname'=>$comp->name)}">{$comp->name|eschtml}</a></li>
            {/foreach}
            </ul>
        {else}
            No Globals
        {/if}
    </div>

{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}


