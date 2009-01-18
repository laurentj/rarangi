{if $project}
    <h2>{jlocale 'default.packages.title', array($project->name)}</h2>

    <div class="block">
    {if $packages}
        <ul>
        {foreach $packages as $package}
            <li><a href="{jurl 'packages:details', array('project'=>$project->name, 'package'=>$package->name)}">{$package->name|eschtml}</a></li>
        {/foreach}
        </ul>
    {else}
        <p>No packages for the project</p>
    {/if}
    </div>
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}
