<div class="monbloc">
{if $package}
    <h2>{jlocale 'default.packages.details.title', array($package->name, $project->name)}</h2>
    
    <div class="block">
    <h3>{jlocale 'default.package.informations.title', array($package->name)}</h3>
    <ul>
        <li>{if $interfaces && $interfaces->rowCount()}<a href="#interfaces">{$interfaces->rowCount()} interfaces</a>{else}no interface{/if}</li>
        <li>{if $classes && $classes->rowCount()}<a href="#classes">{$classes->rowCount()} classes</a>{else}no class{/if}</li>
        <li>{if $functions && $functions->rowCount()}<a href="#functions">{$functions->rowCount()} functions</a>{else}no function{/if}</li>
    </ul>
    </div>

    {if $interfaces && $interfaces->rowCount()}
    <div id="interfaces" class="block">
    <h3>{@default.interfaces.list@}</h3>
    <ul>
    {foreach $interfaces as $class}
        <li><a href="{jurl 'rarangi~components:interfacedetails', array('project'=>$project->name,'package'=>$package->name,'interfacename'=>$class->name)}">{$class->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}


    {if $classes && $classes->rowCount()}
    <div id="classes" class="block">
    <h3>{@default.classes.list@}</h3>
    <ul>
    {foreach $classes as $class}
        <li><a href="{jurl 'rarangi~components:classdetails', array('project'=>$project->name,'package'=>$package->name,'classname'=>$class->name)}">{$class->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}
    
    {if $functions && $functions->rowCount()}
    <div id="functions" class="block">
    <h3>{@default.functions.list@}</h3>
    <ul>
    {foreach $functions as $func}
        <li><a href="{jurl 'rarangi~components:functiondetails', array('project'=>$project->name,'package'=>$package->name,'functionname'=>$func->name)}">{$func->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}
    
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow package {$package->name|eschtml}</p>
    </div>
{/if}
</div>
