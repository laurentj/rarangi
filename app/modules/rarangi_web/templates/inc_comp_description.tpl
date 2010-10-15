
        {if $comp->short_description || $comp->description}
        <div class="ra-short-description">{$comp->short_description|eschtml}</div>
        <div class="ra-text-description">{$comp->description|eschtml}</div>
        {/if}
        {if $comp->internal}<div class="ra-internal-description">{$comp->internal|eschtml}</div>{/if}
        {if $comp->since}<div class="ra-since">Since {$comp->since|eschtml}</div>{/if}