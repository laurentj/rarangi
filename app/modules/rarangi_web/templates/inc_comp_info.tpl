                {if $comp->copyright}
                <div class="copyright">Copyright: {$comp->copyright|eschtml}</div>
                {/if}
                {if $comp->license_label || $comp->license_text}
                <div class="licence">This property has been added under the licence:
                    {if $comp->license_link}
                        <a href="{$class->license_link|eschtml}">{$comp->license_label|eschtml}</a>
                    {else}
                        {$comp->license_label|eschtml}
                    {/if}
                    {if $comp->license_text}
                    <div class="license-description">
                       ererere {$comp->license_text|eschtml}
                    </div>
                    {/if}
                </div>
                {/if}
                {if $comp->links}
                <ul class="links">{foreach $comp->links as $link}
                    <li><a href="{$link[0]|eschtml}">{$link[1]|eschtml}</a></li>{/foreach}
                </ul>
                {/if}
                {if $comp->see}
                <h4>{@default.seealso@}</h4>
                <ul class="see">{foreach $comp->see as $s}
                    <li>{$s|eschtml}</li>{/foreach}
                </ul>
                {/if}
                {if $comp->todo}
                <div class="todo">{$comp->todo|eschtml}</div>
                {/if}
                {if $comp->changelog}
                <div class="changelog">{foreach $comp->changelog as $changelog}
                    <div>{$changelog|eschtml}</div>
                    {/foreach}
                </div>
                {/if}