<div class="monbloc">
{if $package}
    <h2>Project: {$project->name|eschtml}</h2>
    <div class="blockcontent">

    <h3>Informations on the package {$package->name|eschtml}</h3>
    <ul>
        <li>{if $classes}{$classes->rowCount()}{else}0{/if} classes</li>
        <li>{if $functions}{$functions->rowCount()}{else}0{/if} functions</li>
    </ul>

    <h3>List of classes</h3>
    {if $classes->rowCount()}
    <ul>
    {foreach $classes as $class}
        <li><a href="{jurl 'rarangi~components:classdetails', array('project'=>$project->name,'package'=>$package->name,'classname'=>$class->name)}">{$class->name|eschtml}</a></li>
    {/foreach}
    </ul>
    {else}
        <p>No classes in that package</p>
    {/if}
    
    <h3>List of functions</h3>
    {if $functions->rowCount()}
    <ul>
    {foreach $functions as $func}
        <li><a href="{jurl 'rarangi~components:functiondetails', array('project'=>$project->name,'package'=>$package->name,'functionname'=>$func->name)}">{$func->name|eschtml}</a></li>
    {/foreach}
    </ul>
    {else}
        <p>No Functions in that package</p>
    {/if}

    </div>
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow package {$package->name|eschtml}</p>
    </div>
{/if}
</div>
