<div id="ra-page">
{if $func}
    <div id="ra-content">
        <h2>Function: {$func->name|eschtml}</h2>
        {assign $comp = $func}
        <div class="ra-description-block">
            {if $comp->short_description || $comp->description}
            <p class="ra-short-description">{$comp->short_description|eschtml}</p>
            <div class="ra-text-description">{$comp->description|eschtml}</div>
            {/if}
            {if $comp->is_deprecated && $comp->deprecated}<p>About deprecation: {$comp->deprecated|eschtml}</p>{/if}
        </div>

        <div class="ra-tags">
            {if $comp->is_deprecated}<span class="ra-tag-deprecated">deprecated</span>{/if}
            {if $comp->is_experimental}<span class="ra-tag-experimental">experimental</span>{/if}
        </div>

        <div class="ra-block">
            <div class="ra-datatype">Return : {if $comp->return_datatype}{$comp->return_datatype}{else}void{/if}
            {if $comp->return_description}<br/>{$comp->return_description}{/if}</div>
            <dl class="ra-parameters">
                {foreach $comp->parameters as $k=>$param}
                <dt>{$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}</dt>
                <dd>{$param->documentation|eschtml}</dd>
            {/foreach}</dl>
        </div>

        <div class="ra-block">
            <h3 id="ra-internals">Others informations</h3>

            {if $comp->internal}<div class="ra-internal-description">
            <strong>Internal documentation: </strong>
                {$comp->internal|eschtml}</div>{/if}

            <ul>
            {if $comp->copyright}<li><strong>Copyright:</strong> {$comp->copyright|eschtml}</li>{/if}
            {if $comp->license_label || $comp->license_text}
            <li><strong>licence:</strong>
            {if $comp->license_link}
                <a href="{$comp->license_link|eschtml}">{$comp->license_label|eschtml}</a>
            {else}
                {$comp->license_label|eschtml}
            {/if}
            {if $comp->license_text}
            <div class="ra-license-description">{$comp->license_text|eschtml}</div>
            {/if}
            </li>{/if}
            {if $comp->todo}<li class="ra-todo"><strong>todo:</strong> {$comp->todo|eschtml}</li>{/if}
            {if $comp->changelog}
            <li><strong>Changelog:</strong><ul>{foreach $comp->changelog as $changelog}
                    <li>{$changelog|eschtml}</li>{/foreach}
                    </ul>
            </li>{/if}
            </ul>
        </div>
    </div>
    <div id="ra-sidebar">
        <ul class="ra-properties">
            <li><strong>Project:</strong> <a href="{jurl 'rarangi_web~default:project',array('project'=>$project->name)}">{$project->name}</a></li>
            <li><strong>Package:</strong> <a href="{jurl 'rarangi_web~packages:details',array('project'=>$project->name, 'package'=>$package)}">{$package}</a></li>
            {if $comp->fullpath}{* some classes may be referenced but not parsed *}
            <li><strong>File:</strong> <a href="{jurl 'rarangi_web~sources:index',
                        array('project'=>$project->name,
                              'path'=>$comp->fullpath)}#{$comp->line_start}">{$comp->filename}</a></li>
            {if $comp->since}<li><strong>Since:</strong> {$comp->since|eschtml}</li>{/if}
            {/if}
        </ul>

        {if $comp->links || $comp->see || $comp->user_tags}
        <ul class="ra-properties">
            {if $comp->links}
            <li><strong>links: </strong>
                <ul class="ra-links">{foreach $comp->links as $link}
                    <li><a href="{$link[0]|eschtml}">{if $link[1]}{$link[1]|eschtml}{else}{$link[0]|eschtml}{/if}</a></li>{/foreach}
                </ul></li>{/if}
            {if $comp->see}
            <li><strong>{@default.seealso@}: </strong>
                <ul class="ra-see">{foreach $comp->see as $s}
                    <li>{$s|eschtml}</li>{/foreach}
                </ul></li>{/if}
            {if $comp->user_tags}
            {foreach $comp->user_tags as $t=>$c}
            <li><strong>{$t|eschtml}{if $c}: {/if}</strong>{$c|eschtml}</li>
            {/foreach}{/if}
        </ul>
        {/if}
    </div>
{else}
    <h2>Function: {$functionname|eschtml}</h2>
    <div class="ra-block">
        <p>Error, unknow function</p>
    </div>
{/if}
</div>