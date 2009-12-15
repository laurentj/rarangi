{if $project}
    <h2>{@rarangi_web~default.project.details@}</h2>
    
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
