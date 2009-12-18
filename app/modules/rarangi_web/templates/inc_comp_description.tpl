
        {if $comp->short_description || $comp->description}
        <div class="short-description">{$comp->short_description|eschtml}</div>
        <div class="text-description">{$comp->description|eschtml}</div>
        {/if}
        {if $comp->internal}<div class="internal-description">{$comp->internal|eschtml}</div>{/if}
        {if $comp->since}<div class="since">Since {$comp->since|eschtml}</div>{/if}