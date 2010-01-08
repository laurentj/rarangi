{if $comp}
  <div id="content">
    <h2>Constant: {$comp->name|eschtml}</h2>

        <div class="description-block">
        {if $comp->short_description || $comp->description}
        <p class="short-description">{$comp->short_description|eschtml}</p>
        <div class="text-description">{$comp->description|eschtml}</div>
        {/if}
        {if $comp->is_deprecated && $comp->deprecated}<p>About deprecation: {$comp->deprecated|eschtml}</p>{/if}
        </div>

        <div class="tags">
        {if $comp->is_deprecated}<span class="tag-deprecated">deprecated</span>{/if}
        {if $comp->is_experimental}<span class="tag-experimental">experimental</span>{/if}
        </div>

        <div class="block">
            <strong>Value: </strong> <code>{$comp->default_value|eschtml}</code>
        </div>

        <div class="block">
        <h3 id="internals">Others informations</h3>
        
            {if $comp->internal}<div class="internal-description">
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
            <div class="license-description">{$comp->license_text|eschtml}</div>
            {/if}
            </li>{/if}
            {if $comp->todo}<li class="todo"><strong>todo:</strong> {$comp->todo|eschtml}</li>{/if}
            {if $comp->changelog}
            <li><strong>Changelog:</strong><ul>{foreach $comp->changelog as $changelog}
                    <li>{$changelog|eschtml}</li>{/foreach}
                    </ul>
            </li>{/if}
            </ul>
        </div>

  </div>
  <div id="sidebar">
        <ul class="properties">
            <li><strong>Project:</strong> <a href="{jurl 'rarangi_web~default:project',array('project'=>$project->name)}">{$project->name}</a></li>
            <li><strong>Package:</strong> <a href="{jurl 'rarangi_web~packages:details',array('project'=>$project->name, 'package'=>$package)}#globals">{$package}</a></li>
            {if $comp->fullpath}{* some classes may be referenced but not parsed *}
            <li><strong>File:</strong> <a href="{jurl 'rarangi_web~sources:index',
                        array('project'=>$project->name,
                              'path'=>$comp->fullpath)}#{$comp->line_start}">{$comp->filename}</a></li>
            {if $comp->since}<li><strong>Since:</strong> {$comp->since|eschtml}</li>{/if}
            {/if}
        </ul>

        <ul class="properties">
            {if $comp->links}
            <li><strong>links: </strong>
                <ul class="links">{foreach $comp->links as $link}
                    <li><a href="{$link[0]|eschtml}">{if $link[1]}{$link[1]|eschtml}{else}{$link[0]|eschtml}{/if}</a></li>{/foreach}
                </ul></li>{/if}
            {if $comp->see}
            <li><strong>{@default.seealso@}: </strong>
                <ul class="see">{foreach $comp->see as $s}
                    <li>{$s|eschtml}</li>{/foreach}
                </ul></li>{/if}
            {foreach $comp->user_tags as $t=>$c}
            <li><strong>{$t|eschtml}{if $c}: {/if}</strong>{$c|eschtml}</li>
            {/foreach}
        </ul>

  </div>
{else}
    <h2>Constant: {$compname}</h2>
    <div class="blockcontent">
        <p>Error, unknow constant</p>
    </div>
{/if}