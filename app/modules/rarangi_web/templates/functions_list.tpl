<div id="ra-page">
{if $project}
    {if $package}
        <h2>Project: {$project->name|eschtml} - Package: {$package->name|eschtml}</h2>
        <div id="ra-functions" class="ra-block">
        <h3>{@rarangi_web~default.functions.list@}</h3>
        {if $functions && $functions->rowCount()}
            <ul>
            {foreach $functions as $func}
                <li><a href="{jurl 'rarangi_web~components:functiondetails', array('project'=>$project->name,'package'=>$package->name,'functionname'=>$func->name)}">{$func->name|eschtml}</a></li>
            {/foreach}
            </ul>
        {else}
            <p>No function in the package {$package->name|eschtml}</p>
        {/if}
        </div>
    {else}
        <h2>Project: {$project->name|eschtml} - Package: {$packagename|eschtml}</h2>
        <div class="ra-block">
            <p>Error, unknow package</p>
        </div>
    {/if}
{else}
    <h2>Project: {$projectname}</h2>
    <div class="ra-block">
    <p>Error, unknow project</p>
    </div>
{/if}
</div>
