<div class="monbloc">
{if $package}
    <h2>{jlocale 'default.packages.details.title', array($package->name, $project->name)}</h2>
    
    <div class="block">
    <h3>{jlocale 'default.package.informations.title', array($package->name)}</h3>
    <ul>
        <li>{if $classes}{$classes->rowCount()}{else}0{/if} classes</li>
        <li>{if $functions}{$functions->rowCount()}{else}0{/if} functions</li>
    </ul>
    </div>

    <div class="block">
    <h3>{@default.classes.list@}</h3>
    {if $classes->rowCount()}
    <ul>
    {foreach $classes as $class}
        <li><a href="{jurl 'rarangi~components:classdetails', array('project'=>$project->name,'package'=>$package->name,'classname'=>$class->name)}">{$class->name|eschtml}</a></li>
    {/foreach}
    </ul>
    {else}
        <p>{@default.classes.list.empty@}/p>
    {/if}
    </div>
    
    <div class="block">
    <h3>{@default.functions.list@}</h3>
    {if $functions->rowCount()}
    <ul>
    {foreach $functions as $func}
        <li><a href="{jurl 'rarangi~components:functiondetails', array('project'=>$project->name,'package'=>$package->name,'functionname'=>$func->name)}">{$func->name|eschtml}</a></li>
    {/foreach}
    </ul>
    {else}
        <p>{@default.functions.list.empty@}</p>
    {/if}
    </div>
    
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow package {$package->name|eschtml}</p>
    </div>
{/if}
</div>
