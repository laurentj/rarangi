<div id="ra-page">
{if $project}
    <h2>{@rarangi_web~default.project.details@}</h2>
    
    <div class="ra-block">
    <h3>{@rarangi_web~default.project.code.stats@}</h3>
    <ul>
        <li><a href="{jurl 'rarangi_web~sources:index', array('project'=>$projectname)}">{$files_counter} files</a></li>
        <li>{$lines_counter} lines of code</li>
        {if $errors_counter}<li><a href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname)}">{$errors_counter} errors/warnings</a> found during the parsing</li>{/if}
    </ul>
    </div>
    
    <div class="ra-block">
    <h3>{@rarangi_web~default.project.components.stats@}</h3>
    <ul>
        <li><a href="{jurl 'rarangi_web~packages:index', array('project'=>$projectname)}">{$packages_counter} packages</a></li>
        <li>{$classes_counter} classes</li>
        <li>{$functions_counter} functions</li>
    </ul>
    </div>

    {if $authors->rowCount() > 0}
    <div class="ra-block">
    <h3>{@rarangi_web~default.authors@}</h3>
    <ul id="ra-authors">
    {foreach $authors as $author}
        <li>{$author->name|eschtml}</li>
    {/foreach}
    </ul>
    </div>
    {/if}
{else}
    <h2>Project: {$projectname}</h2>
    <div class="ra-block">
        <p>Error, unknow project</p>
    </div>
{/if}
</div>