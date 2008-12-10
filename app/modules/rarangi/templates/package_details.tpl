<div class="monbloc">
{if $project}
    <h2>Project: {$project->name|eschtml}</h2>
    <div class="blockcontent">

    <h3>Informations on the package</h3>
    <ul>
        <li>{if $subpackages}{$subpackages->rowCount()}{else}0{/if} subpackages</li>
        <li>{if $classes}{$classes->rowCount()}{else}0{/if} classes</li>
        <li>{if $functions}{$functions->rowCount()}{else}0{/if} functions</li>
    </ul>
    
    <h3>List of subpackages</h3>
    <p>here list of subpackage (but we can't at the moment)</p>
    
    <h3>List of classes</h3>
    {if $classes}
    <ul>
    {foreach $classes as $class}
        <li>{$class->name|eschtml}</li>
    {/foreach}
    </ul>
    {else}
        <p>No classes in that package</p>
    {/if}
    
    <h3>List of functions</h3>
    <p>here list of function (but we can't at the moment)</p>

    </div>
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}
</div>