
        {if $comp->short_description || $comp->description}
        <div id="short-description">{$comp->short_description|eschtml}</div>
        <div id="text-description">{$comp->description|eschtml}</div>
        {else}
        <div id="text-description">{@default.nodescription@}</div>
        {/if}
        {if $comp->internal}<div class="internal-description">{$comp->internal|eschtml}</div>{/if}
        {if $comp->since}<div class="since">Since {$comp->since|eschtml}</div>{/if}