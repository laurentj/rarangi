<div class="monbloc">
{if $project}
    <h2>Project: {$project->name|eschtml}</h2>
    <div class="blockcontent">

    {if $packages}
        <h3>Packages</h3>
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
</div>
