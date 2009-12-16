{if $func}
    <h2>Function: {$func->name|eschtml}</h2>
        <div class="block">
        <h3 id="description">{@rarangi_web~default.description@}</h3>
        {assign $comp = $func}
        {include 'inc_comp_description'}
        </div>

        <div class="block">
        <div class="datatype">Return : {if $comp->return_datatype}{$comp->return_datatype}{else}void{/if}
        {if $comp->return_description}<br/>{$comp->return_description}{/if}</div>
        <dl class="parameters">
            {foreach $function_parameters as $k=>$param}
            <dt>{$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}</dt>
            <dd>{$param->documentation|eschtml}</dd>
        {/foreach}</dl>
        </div>

        <div class="block">
        <h3 id="informations">Development Informations</h3>
        <div class="development-info">
            <div class="file-info">
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
    <div class="blockcontent">
        <p>Error, unknow function</p>
    </div>
{/if}
