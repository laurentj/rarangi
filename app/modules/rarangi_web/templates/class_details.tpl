{if $class}
  <div id="content">
    <h2>{if $class->is_interface}Interface{else}Class{/if}: {$class->name|eschtml}</h2>

        <div class="description-block">

        {assign $comp = $class}
        {include 'inc_comp_description'}

        </div>

        <div class="tags">     
        {if $class->is_abstract}
        <span class="tag-abstract">abstract</span>
        {/if}
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
                <td><a href="#p-{$p->name}">{if $p->type != 2}${/if}{$p->name}</a></td>
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


  </div>
  <div id="sidebar">
        <ul class="properties">
            <li><strong>Project:</strong> <a href="{jurl 'rarangi_web~default:project',array('project'=>$project->name)}">{$project->name}</a></li>
            <li><strong>Package:</strong> <a href="{jurl 'rarangi_web~packages:details',array('project'=>$project->name, 'package'=>$package)}">{$package}</a></li>
            {if $class->fullpath}{* some classes may be referenced but not parsed *}
            <li><strong>File:</strong> <a href="{jurl 'rarangi_web~sources:index',
                        array('project'=>$project->name,
                              'path'=>$class->fullpath)}#{$class->line_start}">{$class->filename}</a></li>
            {if $class->since}<ul><strong>Since:</strong> {$class->since|eschtml}</li>{/if}
            {/if}
            {if $class->copyright}<li><strong>Copyright:</strong> {$class->copyright|eschtml}</li>{/if}
            {if $class->license_label || $comp->license_text}
            <li><strong>licence:</strong>
                    {if $comp->license_link}
                        <a href="{$class->license_link|eschtml}">{$comp->license_label|eschtml}</a>
                    {else}
                        {$comp->license_label|eschtml}
                    {/if}
                    {if $comp->license_text}
                    <div class="license-description">{$comp->license_text|eschtml}</div>
                    {/if}
            </li>{/if}
            {if $class->links}
            <li><ul class="links">{foreach $comp->links as $link}
                    <li><a href="{$link[0]|eschtml}">{$link[1]|eschtml}</a></li>{/foreach}
                </ul></li>
                {/if}
            {if $comp->see}
            <li><strong>{@default.seealso@}: </strong>
                <ul class="see">{foreach $comp->see as $s}
                    <li>{$s|eschtml}</li>{/foreach}
                </ul></li>
            {/if}
            {if $comp->todo}<li class="todo">{$comp->todo|eschtml}</li>{/if}
            {if $comp->changelog}
            <li>{foreach $comp->changelog as $changelog}
                    <div>{$changelog|eschtml}</div>{/foreach}
            </li>{/if}
        </ul>
        
        
        <div class="relationship">
        <ul>
            <li><strong>Inherits from: </strong>
            {if $class->mother_class}
            {if $class->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$package,'interfacename'=>$class->mother_class_name)}">{$class->mother_class_name}</a>
            {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$package,'classname'=>$class->mother_class_name)}">{$class->mother_class_name}</a>
            {/if}
            {/if}</li>
            <li><strong>Inherited by: </strong> </li>
            <li><strong>Returned by: </strong> </li>
            <li><strong>As parameter for: </strong> </li>
        </ul>
        </div>
  </div>
{else}
    <h2>Class: {$classname}</h2>
    <div class="blockcontent">
        <p>Error, unknow class</p>
    </div>
{/if}
