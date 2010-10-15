<div id="ra-page">
{if $package}
    <h2>{jlocale 'rarangi_web~default.packages.details.title', array($package->name, $project->name)}</h2>
    
    <div class="ra-block">
    <h3>{jlocale 'rarangi_web~default.package.informations.title', array($package->name)}</h3>
    <ul>
        <li>{if $interfaces && $interfaces->rowCount()}<a href="#ra-interfaces">{$interfaces->rowCount()} interfaces</a>{else}{@rarangi_web~default.interfaces.list.empty@}{/if}</li>
        <li>{if $classes && $classes->rowCount()}<a href="#ra-classes">{$classes->rowCount()} classes</a>{else}{@rarangi_web~default.classes.list.empty@}{/if}</li>
        <li>{if $functions && $functions->rowCount()}<a href="#ra-functions">{$functions->rowCount()} functions</a>{else}{@rarangi_web~default.functions.list.empty@}{/if}</li>
        <li>{if $globals && $globals->rowCount()}<a href="#ra-globals">{$globals->rowCount()} global variables</a>{else}{@rarangi_web~default.globals.list.empty@}{/if}</li>
        <li>{if $defines && $defines->rowCount()}<a href="#ra-constants">{$defines->rowCount()} constants</a>{else}{@rarangi_web~default.constants.list.empty@}{/if}</li>
    </ul>
    </div>

    {if $interfaces && $interfaces->rowCount()}
    <div id="ra-interfaces" class="ra-block">
    <h3>{@rarangi_web~default.interfaces.list@}</h3>
    <ul>
    {foreach $interfaces as $class}
        <li><a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$package->name,'interfacename'=>$class->name)}">{$class->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}


    {if $classes && $classes->rowCount()}
    <div id="ra-classes" class="ra-block">
    <h3>{@rarangi_web~default.classes.list@}</h3>
    <ul>
    {foreach $classes as $class}
        <li><a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$package->name,'classname'=>$class->name)}">{$class->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}
    
    {if $functions && $functions->rowCount()}
    <div id="ra-functions" class="ra-block">
    <h3>{@rarangi_web~default.functions.list@}</h3>
    <ul>
    {foreach $functions as $func}
        <li><a href="{jurl 'rarangi_web~components:functiondetails', array('project'=>$project->name,'package'=>$package->name,'functionname'=>$func->name)}">{$func->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}

    {if $globals && $globals->rowCount()}
    <div id="ra-globals" class="ra-block">
    <h3>{@rarangi_web~default.globals.list@}</h3>
    <ul>
    {foreach $globals as $glob}
        <li><a href="{jurl 'rarangi_web~components:globaldetails', array('project'=>$project->name,'package'=>$package->name, 'globalname'=>$glob->name)}">{$glob->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}

    {if $defines && $defines->rowCount()}
    <div id="ra-constants" class="ra-block">
    <h3>{@rarangi_web~default.constants.list@}</h3>
    <ul>
    {foreach $defines as $def}
        <li><a href="{jurl 'rarangi_web~components:constantdetails', array('project'=>$project->name,'package'=>$package->name, 'constantname'=>$def->name)}">{$def->name|eschtml}</a></li>
    {/foreach}
    </ul>
    </div>
    {/if}
    
{else}
    <h2>Project: {$projectname}</h2>
    <div class="ra-block">
        <p>Error, unknow package {$package->name|eschtml}</p>
    </div>
{/if}
</div>