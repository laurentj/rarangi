<div id="ra-page">
{if $class}
  <div id="ra-content">
    <h2>{if $class->is_interface}Interface{else}Class{/if}: {$class->name|eschtml}</h2>

        <div class="ra-description-block">
        {assign $comp = $class}
        {if $comp->short_description || $comp->description}
        <p class="ra-short-description">{$comp->short_description|eschtml}</p>
        <div class="ra-text-description">{$comp->description|eschtml}</div>
        {/if}
        {if $comp->is_deprecated && $comp->deprecated}<p>About deprecation: {$comp->deprecated|eschtml}</p>{/if}
        </div>

        <div class="ra-tags">     
        {if $class->is_abstract}<span class="ra-tag-abstract">abstract</span>{/if}
        {if $comp->is_deprecated}<span class="ra-tag-deprecated">deprecated</span>{/if}
        {if $comp->is_experimental}<span class="ra-tag-experimental">experimental</span>{/if}
        </div>
        
        {if count($properties) || count($methods)}
        <div class="ra-block">
        <h3 id="ra-summary">Summary</h3>
        <div class="ra-class-summary">
            {if !$class->is_interface}
            <table class="ra-properties-list">
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

            <table class="ra-methods-list">
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
        <div class="ra-block">
        <h3 id="ra-properties">List of properties</h3>
        <div class="ra-class-properties-list">
            {if count($properties)}
              {foreach $properties as $comp}
              <div class="ra-class-property">
              <h4 id="p-{$comp->name}"><a name="p-{$comp->name}"></a>{$comp->name}</h4>
                {include 'inc_comp_description'}

                {if $comp->datatype}<p class="ra-datatype">Datatype : {$comp->datatype}</p>{/if}
                <p class="ra-type-access">{if $comp->type == 1}static{elseif $comp->type == 2}const{/if}
                   {if $comp->accessibility == 'PRO'}protected{elseif $comp->accessibility=='PRI'}private{else}public{/if}</p>

                <div class="ra-tags">
                {if $comp->is_deprecated}<span class="ra-tag-deprecated">deprecated</span>{/if}
                {if $comp->is_experimental}<span class="ra-tag-experimental">experimental</span>{/if}
                </div>
                {if $comp->is_deprecated && $comp->deprecated}<p>About deprecation: {$comp->deprecated|eschtml}</p>{/if}
                {include 'inc_comp_info'}

              </div>
              {/foreach}
            {else}
            No properties
            {/if}
        </div>
        </div>
        {/if}
        
        <div class="ra-block">
        <h3 id="ra-methods">List of methods</h3>
        <div class="ra-class-methods-list">
            {if count($methods)}
              {foreach $methods as $comp}
              <div class="ra-method-details">
              <h4 id="m-{$comp->name}">{$comp->name}</h4>
                {include 'inc_comp_description'}
                <div class="ra-datatype">Return : {if $comp->return_datatype}{$comp->return_datatype}{else}void{/if}
                {if $comp->return_description}<br/>{$comp->return_description}{/if}</div>
                <p class="ra-type-access">{if $comp->is_static == 1}static{/if} {if $comp->is_final == 1}final{/if}
                {if $comp->is_abstract == 1}abstract{/if}
                   {if $comp->accessibility == 'PRO'}protected{elseif $comp->accessibility=='PRI'}private{else}public{/if}</p>
                <dl class="ra-parameters">
                    {foreach $method_parameters[$comp->name] as $k=>$param}
                    <dt>{$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}</dt>
                    <dd>{$param->documentation|eschtml}</dd>
                {/foreach}</dl>
                <div class="ra-tags">
                {if $class->is_deprecated}<span class="ra-tag-deprecated">deprecated</span>{/if}
                {if $class->is_experimental}<span class="ra-tag-experimental">experimental</span>{/if}
                </div>
                {if $class->is_deprecated && $class->deprecated}<p>About deprecation: {$class->deprecated|eschtml}</p>{/if}
                {include 'inc_comp_info'}
              </div>
              {/foreach}
            {else}
            No methods
            {/if}
        </div>
        </div>

        <div class="ra-block">
        <h3 id="ra-internals">Others informations</h3>
        
            {if $class->internal}<div class="ra-internal-description">
            <strong>Internal documentation: </strong>
                {$class->internal|eschtml}</div>{/if}
            
            <ul>
            {if $class->copyright}<li><strong>Copyright:</strong> {$class->copyright|eschtml}</li>{/if}
            {if $class->license_label || $class->license_text}
            <li><strong>licence:</strong>
            {if $class->license_link}
                <a href="{$class->license_link|eschtml}">{$class->license_label|eschtml}</a>
            {else}
                {$class->license_label|eschtml}
            {/if}
            {if $class->license_text}
            <div class="ra-license-description">{$class->license_text|eschtml}</div>
            {/if}
            </li>{/if}
            {if $class->todo}<li class="ra-todo"><strong>todo:</strong> {$class->todo|eschtml}</li>{/if}
            {if $class->changelog}
            <li><strong>Changelog:</strong><ul>{foreach $class->changelog as $changelog}
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
            {if $class->fullpath}{* some classes may be referenced but not parsed *}
            <li><strong>File:</strong> <a href="{jurl 'rarangi_web~sources:index',
                        array('project'=>$project->name,
                              'path'=>$class->fullpath)}#{$class->line_start}">{$class->filename}</a></li>
            {if $class->since}<li><strong>Since:</strong> {$class->since|eschtml}</li>{/if}
            {/if}
        </ul>

        <div class="ra-relationship">
        <ul>
            {if $class->is_interface}
            <li><strong>Implemented by:</strong> 
            {else}
            <li><strong>Implements:</strong>
            {/if}{foreach $rel_implementation as $impl}
                {if $class->is_interface}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$impl->package,'classname'=>$impl->name)}">{$impl->name}</a>
                {else}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$impl->package,'interfacename'=>$impl->name)}">{$impl->name}</a>
                {/if}
            {/foreach}
            </li>
            <li><strong>Inherits from: </strong>
            {if $mother_class}
                {if $mother_class->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$mother_class->package,'interfacename'=>$mother_class->name)}">{$mother_class->name}</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$mother_class->package,'classname'=>$mother_class->name)}">{$mother_class->name}</a>
                {/if}
            {/if}</li>
            <li><strong>Inherited by: </strong>
            {foreach $descendants as $desc}
                {if $class->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$desc->package,'interfacename'=>$desc->name)}">{$desc->name}</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$desc->package,'classname'=>$desc->name)}">{$desc->name}</a>
                {/if}
            {/foreach}
            </li>
            <li><strong>Returned by: </strong>
            {foreach $asfunctionreturn as $fct}
            <a href="{jurl 'rarangi_web~components:functiondetails', array('project'=>$project->name,'package'=>$fct->package,'functionname'=>$fct->name)}">{$fct->name}()</a>
            {/foreach}
            {foreach $asmethodreturn as $meth}
                {if $meth->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$meth->package,'interfacename'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$meth->package,'classname'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {/if}
            {/foreach}

            </li>
            <li><strong>As parameter for: </strong>
            {foreach $asfunctionparameter as $fct}
            <a href="{jurl 'rarangi_web~components:functiondetails', array('project'=>$project->name,'package'=>$fct->package,'functionname'=>$fct->name)}">{$fct->name}()</a>
            {/foreach}
            {foreach $asmethodparameter as $meth}
                {if $meth->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$meth->package,'interfacename'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$meth->package,'classname'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {/if}
            {/foreach}
            </li>
        </ul>
        </div>

        <ul class="ra-properties">
            {if $class->links}
            <li><strong>links: </strong>
                <ul class="ra-links">{foreach $class->links as $link}
                    <li><a href="{$link[0]|eschtml}">{if $link[1]}{$link[1]|eschtml}{else}{$link[0]|eschtml}{/if}</a></li>{/foreach}
                </ul></li>{/if}
            {if $class->see}
            <li><strong>{@default.seealso@}: </strong>
                <ul class="ra-see">{foreach $class->see as $s}
                    <li>{$s|eschtml}</li>{/foreach}
                </ul></li>{/if}
            {if $class->user_tags}
            {foreach $class->user_tags as $t=>$c}
            <li><strong>{$t|eschtml}{if $c}: {/if}</strong>{$c|eschtml}</li>
            {/foreach}{/if}
        </ul>
  </div>
{else}
    <h2>Class: {$classname}</h2>
    <div class="ra-block">
        <p>Error, unknow class</p>
    </div>
{/if}
  </div>
