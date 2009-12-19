<div class="monbloc">
{if $project}
    {if $package}
        <h2>Project: {$project->name|eschtml} - Package: {$package->name|eschtml}</h2>
        {if !$forInterfaces}
        <div id="classes" class="block">
        <h3>{@rarangi_web~default.classes.list@}</h3>
        {if $classes && $classes->rowCount()}
            <ul>
            {foreach $classes as $class}
                <li><a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$package->name,'classname'=>$class->name)}">{$class->name|eschtml}</a></li>
            {/foreach}
            </ul>
        {else}
            <p>No classes in the package {$packagename}</p>
        {/if}
        </div>
        {else}
            <p>Interfaces</p>
        {/if}
    {else}
        <h2>Project: {$project->name|eschtml} - Package: {$packagename|eschtml}</h2>
        <div class="blockcontent">
            <p>Error, unknow package</p>
        </div>
    {/if}
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}
</div>
