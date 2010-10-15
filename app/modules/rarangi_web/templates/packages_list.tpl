<div id="ra-page">
{if $project}
    <h2>{jlocale 'rarangi_web~default.packages.title', array($project->name)}</h2>

    <div class="ra-block">
    {if $packages}
        <ul>
        {foreach $packages as $package}
            <li><a href="{jurl 'rarangi_web~packages:details', array('project'=>$project->name, 'package'=>$package->name)}">{$package->name|eschtml}</a></li>
        {/foreach}
        </ul>
    {else}
        <p>No packages for the project</p>
    {/if}
    </div>
{else}
    <h2>Project: {$projectname}</h2>
    <div class="ra-block">
        <p>Error, unknow project</p>
    </div>
{/if}
</div>