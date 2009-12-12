{if $class}
    <h2>{if $class->is_interface}Interface{else}Class{/if}: {$class->name|eschtml}</h2>
        <div class="block">
        <h3 id="description">{@default.description@}</h3>
        {if $class->mother_class}
        <div class="class-inheriting">
            This {if $class->is_interface}interface{else}class{/if} extends
            {if $class->is_interface}
            <a href="{jurl 'rarangi~components:interfacedetails', array('project'=>$project->name,'package'=>$package,'interfacename'=>$class->mother_class_name)}">{$class->mother_class_name}</a>
            {else}
            <a href="{jurl 'rarangi~components:classdetails', array('project'=>$project->name,'package'=>$package,'classname'=>$class->mother_class_name)}">{$class->mother_class_name}</a>
            {/if}
        </div>
        {/if}

        {if $class->is_abstract}
        <div class="class-abstract">
            {@default.class.isabstract@}
        </div>
        {/if}
        {assign $comp = $class}
        {include 'inc_comp_description'}
        </div>
        
        {if count($properties) || count($methods)}
        <div class="block">
        <h3 id="summary">Summary</h3>
        <div class="class-summary">
            {if !$class->is_interface}
            <table class="properties-list">
              {foreach $properties as $p}
              <tr>
                <td>{if $p->type == 1}static{elseif $p->type == 2}const{/if}
                   {if $p->accessibility == 'PRO'}protected{elseif $p->accessibility=='PRI'}private{else}public{/if}
                   {$p->datatype}</td>
                <td><a href="#p-{$p->name}">{$p->name}</a></td>
                <td>{$p->short_description|eschtml}</td>
              </tr>
              {/foreach}
            </table>
            {/if}

            <table class="methods-list">
              {foreach $methods as $m}
              <tr>
                <td>{if $m->is_static == 1}static{/if} {if $m->is_final == 1}final{/if} {if $m->is_abstract == 1}abstract{/if}
                   {if $m->accessibility == 'PRO'}protected{elseif $m->accessibility=='PRI'}private{else}public{/if}
                   {$m->return_datatype}</td>
                <td><a href="#m-{$m->name}">{$m->name}</a>
                ({foreach $method_parameters[$m->name] as $k=>$param}{if $k},{/if}
                {$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}
                {/foreach})</td>
                <td>{$m->short_description|eschtml}</td>
              </tr>
              {/foreach}
            </table>
        </div>
        </div>
        {/if}

        {if !$class->is_interface}
        <div class="block">
        <h3 id="properties">List of properties</h3>
        <div class="class-properties-list">
            {if count($properties)}
              {foreach $properties as $comp}
              <div class="class-property">
              <h4 id="p-{$comp->name}"><a name="p-{$comp->name}"></a>{$comp->name}</h4>
                {include 'inc_comp_description'}

                <p class="datatype">Datatype : {if $comp->datatype}{$comp->datatype}{else}undefined{/if}</p>
                <p class="type-access">{if $comp->type == 1}static{elseif $comp->type == 2}const{/if}
                   {if $comp->accessibility == 'PRO'}protected{elseif $comp->accessibility=='PRI'}private{else}public{/if}</p>

                {include 'inc_comp_info'}

              </div>
              {/foreach}
            {else}
            No properties
            {/if}
        </div>
        </div>
        {/if}
        
        <div class="block">
        <h3 id="methods">List of methods</h3>
        <div class="class-methods-list">
            {if count($methods)}
              {foreach $methods as $comp}
              <div class="method-details">
              <h4 id="m-{$comp->name}">{$comp->name}</h4>
                {include 'inc_comp_info'}
                <div class="datatype">Return : {if $comp->return_datatype}{$comp->return_datatype}{else}void{/if}
                {if $comp->return_description}<br/>{$comp->return_description}{/if}</div>
                <p class="type-access">{if $comp->is_static == 1}static{/if} {if $comp->is_final == 1}final{/if}
                {if $comp->is_abstract == 1}abstract{/if}
                   {if $comp->accessibility == 'PRO'}protected{elseif $comp->accessibility=='PRI'}private{else}public{/if}</p>
                <dl class="parameters">
                    {foreach $method_parameters[$comp->name] as $k=>$param}
                    <dt>{$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}</dt>
                    <dd>{$param->documentation|eschtml}</dd>
                {/foreach}</dl>
                {include 'inc_comp_info'}
              </div>
              {/foreach}
            {else}
            No methods
            {/if}
        </div>
        </div>

        <div class="block">
        <h3 id="relation">Relation to other components</h3>
        <ul>
            <li>TODO: list of methods/functions which return this class</li>
            <li>TODO: list of methods/functions which accept this class as parameter</li>
            <li>TODO: list of classes which inherits from this class</li>
        </ul>
        </div>

        <div class="block">
        <h3 id="informations">Development Informations</h3>
        <div class="development-info">
            <div class="file-info">
                {if $class->fullpath}
                Defined in the file <a href="{jurl 'sources:index',
                                    array('project'=>$project->name,'path'=>$class->fullpath)}#{$class->line_start}">{$class->fullpath}</a>
                {if $class->since}
                since {$class->since|eschtml}
                {/if}
                {else}
                This class isn't defined in any file of the project.
                {/if}
            </div>
            {assign $comp = $class}
            {include 'inc_comp_info'}
        </div>
        </div>
{else}
    <h2>Class: {$classname}</h2>
    <div class="blockcontent">
        <p>Error, unknow class</p>
    </div>
{/if}
