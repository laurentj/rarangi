{if $project}
    <h2>{@rarangi_web~default.errors.list@}</h2>
    
    <div class="block">

    <dl>
        {foreach $errors as $err}
        <dt class="message-{$err->type}">{$err->type}: {$err->message|eschtml}</dt>
        <dd>{$err->file} {$err->line}</dd>
        {/foreach}
    </dl>
    </div>

{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}
