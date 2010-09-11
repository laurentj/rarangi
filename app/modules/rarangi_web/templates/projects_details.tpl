{if $project}
    <h2>{@rarangi_web~default.project.details@}</h2>
    
    <div class="block">
    <h3>{@rarangi_web~default.project.code.stats@}</h3>
    <ul>
        <li>{$files_counter} files</li>
        <li>{$lines_counter} lines of code</li>
        {if $errors_counter}<li><a href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname)}">{$errors_counter} errors/warnings</a> found during the parsing</li>{/if}
    </ul>
    </div>
    
    <div class="block">
    <h3>{@rarangi_web~default.project.components.stats@}</h3>
    <ul>
        <li>{$classes_counter} classes</li>
        <li>{$functions_counter} functions</li>
        <li>{$packages_counter} packages</li>
    </ul>
    </div>

    {if $authors->rowCount() > 0}
    <div class="block">
    <h3>{@rarangi_web~default.authors@}</h3>
    <ul id="authors">
    {foreach $authors as $author}
        <li>{$author->name|eschtml} {if $author->email!=''}&lt;{$author->email|eschtml}&gt;{/if}</li>
    {/foreach}
    </ul>
    </div>
    {/if}
{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}
