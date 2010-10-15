<div id="ra-page">
{if $func}
    <h2>Function: {$func->name|eschtml}</h2>
        <div class="ra-block">
        <h3 id="ra-description">{@rarangi_web~default.description@}</h3>
        {assign $comp = $func}
        {include 'inc_comp_description'}
        {if $func->is_deprecated && $func->deprecated}<p>About deprecation: {$func->deprecated|eschtml}</p>{/if}
        <div class="ra-tags">
        {if $func->is_deprecated}<span class="ra-tag-deprecated">deprecated</span>{/if}
        {if $func->is_experimental}<span class="ra-tag-experimental">experimental</span>{/if}
        </div>

        </div>

        <div class="ra-block">
            <div class="ra-datatype">Return : {if $comp->return_datatype}{$comp->return_datatype}{else}void{/if}
            {if $comp->return_description}<br/>{$comp->return_description}{/if}</div>
            <dl class="ra-parameters">
                {foreach $function_parameters as $k=>$param}
                <dt>{$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}</dt>
                <dd>{$param->documentation|eschtml}</dd>
            {/foreach}</dl>
        </div>

        <div class="ra-block">
            <h3 id="ra-informations">Development Informations</h3>
            <div class="ra-development-info">
                <div class="ra-file-info">
                    {if $func->fullpath}
                    Defined in the file <a href="{jurl 'rarangi_web~sources:index',
                                        array('project'=>$project->name,'path'=>$func->fullpath)}#{$func->line_start}">{$func->fullpath}</a>
                    {if $func->since}
                    since {$func->since|eschtml}
                    {/if}
                    {else}
                    This function isn't defined in any file of the project.
                    {/if}
                </div>
                {include 'inc_comp_info'}
            </div>
        </div>

{else}
    <h2>Function: {$functionname|eschtml}</h2>
    <div class="ra-block">
        <p>Error, unknow function</p>
    </div>
{/if}
</div>