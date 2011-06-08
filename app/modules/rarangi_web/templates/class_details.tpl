<div id="ra-page">
{if $class}
    <div id="ra-content">
        <h2>{if $class->is_interface}Interface{else}Class{/if}: {$class->name|eschtml}</h2>
        {assign $comp = $class}
        <div class="ra-description-block">
            {if $comp->short_description || $comp->description}
            <p class="ra-short-description">{$comp->short_description|eschtml}</p>
            <div class="ra-text-description">{$comp->description|eschtml}</div>
            {/if}
            {if $comp->is_deprecated && $comp->deprecated}<p>About deprecation: {$comp->deprecated|eschtml}</p>{/if}
        </div>

        <div class="ra-tags">
            {if $comp->is_abstract}<span class="ra-tag-abstract">abstract</span>{/if}
            {if $comp->is_deprecated}<span class="ra-tag-deprecated">deprecated</span>{/if}
            {if $comp->is_experimental}<span class="ra-tag-experimental">experimental</span>{/if}
        </div>

        {if !$class->is_interface}
        <div class="ra-block" id="ra-class-properties">
            <h3>List of properties</h3>
            <div class="ra-class-properties">
            {if count($class->properties)}
              {foreach $class->properties as $comp}
              <div class="ra-class-property">
                <h4 id="p-{$comp->name}"><a name="p-{$comp->name}"></a>{if $comp->type != 2}${/if}{$comp->name}</h4>

                {if $comp->short_description || $comp->description}
                <div class="ra-short-description">{$comp->short_description|eschtml}</div>
                <div class="ra-text-description">{$comp->description|eschtml}</div>
                {/if}
                {if $comp->is_deprecated && $comp->deprecated}<p>About deprecation: {$comp->deprecated|eschtml}</p>{/if}

                {if $comp->internal}<div class="ra-internal-description">{$comp->internal|eschtml}</div>{/if}
                {if $comp->since}<div class="ra-since">Since {$comp->since|eschtml}</div>{/if}


                <p class="ra-prototype">
                    <span class="ra-type-access">
                      {if $comp->type == 1}static{elseif $comp->type == 2}const{/if}
                      {if $comp->accessibility == 'PRO'}protected{elseif $comp->accessibility=='PRI'}private{else}public{/if}
                    </span>
                    {if $comp->datatype}<span class="ra-datatype">{$comp->datatype}</span>{/if}
                    <span class="ra-property-name">{if $comp->type != 2}${/if}{$comp->name}</span>
                    {if $comp->defaultvalue != ''}<span class="ra-property-value">{$comp->defaultvalue|eschtml}</span>{/if}
                </p>

                <div class="ra-tags">
                {if $comp->is_deprecated}<span class="ra-tag-deprecated">deprecated</span>{/if}
                {if $comp->is_experimental}<span class="ra-tag-experimental">experimental</span>{/if}
                </div>

                {include 'inc_comp_info'}

              </div>
              {/foreach}
            {else}
            No properties
            {/if}
            </div>
        </div>
        {/if}

        <div class="ra-block" id="ra-class-methods">
            <h3>List of methods</h3>
            <div class="ra-class-methods">
                {if count($class->methods)}
                  {foreach $class->methods as $comp}
                  <div class="ra-method-details" id="m-{$comp->name}">
                    <h4><a name="m-{$comp->name}"></a>{$comp->name}()</h4>
                    {include 'inc_comp_description'}

                    <p class="ra-prototype">
                        <span class="ra-type-access">{if $comp->is_static == 1}static{/if} {if $comp->is_final == 1}final{/if}
                        {if $comp->is_abstract == 1}abstract{/if}
                        {if $comp->accessibility == 'PRO'}protected{elseif $comp->accessibility=='PRI'}private{else}public{/if}</span>
                        <span class="ra-method-return">{if $comp->return_datatype}{$comp->return_datatype}{else}void{/if}</span>
                        <span class="ra-method-name">{$comp->name}</span> (
                        {assign $pNumber=0}
                        {foreach $class->methodParameters[$comp->name] as $k=>$param}
                            {if $k>0},{/if}
                            <span class="ra-method-parameter">
                            {if $param->defaultvalue != ''} {assign $pNumber=$pNumber+1}
                            [{$param->type} <span class="ra-method-parameter-name">${$param->name}</span>
                                = {$param->defaultvalue}
                            {else}
                            {$param->type} <span class="ra-method-parameter-name">${$param->name}</span>
                            {/if}</span>
                        {/foreach}
                        {while $pNumber--}]{/while})</span>
                    </p>


                    <dl class="ra-parameters">
                        {foreach $class->methodParameters[$comp->name] as $k=>$param}
                        {if $param->documentation}
                        <dt>{$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}</dt>
                        <dd>{$param->documentation|eschtml}</dd>{/if}
                    {/foreach}</dl>

                    {if $comp->return_datatype && $comp->return_description}
                    <div class="ra-datatype">Return : {if $comp->return_datatype}{$comp->return_datatype}{else}void{/if}
                    {if $comp->return_description}<br/>{$comp->return_description}{/if}</div>{/if}

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
        {assign $comp = $class}
        <div class="ra-block" id="ra-internals">
            <h3>Others informations</h3>

            {if $comp->internal}
            <div class="ra-internal-description">
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
            <div class="ra-license-description">{$comp->license_text|eschtml}</div>
            {/if}
            </li>{/if}
            {if $comp->todo}<li class="ra-todo"><strong>todo:</strong> {$comp->todo|eschtml}</li>{/if}
            {if $comp->changelog}
            <li><strong>Changelog:</strong><ul>{foreach $comp->changelog as $changelog}
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
            {if $comp->fullpath}{* some classes may be referenced but not parsed *}
            <li><strong>File:</strong> <a href="{jurl 'rarangi_web~sources:index',
                        array('project'=>$project->name,
                              'path'=>$comp->fullpath)}#{$comp->line_start}">{$comp->filename}</a></li>
            {if $comp->since}<li><strong>Since:</strong> {$comp->since|eschtml}</li>{/if}
            {/if}
        </ul>

        <div class="ra-relationship">
        <ul>
            {if $comp->is_interface}
            <li><strong>Implemented by:</strong>
            {else}
            <li><strong>Implements:</strong>
            {/if}{foreach $relations->implementations as $impl}
                {if $comp->is_interface}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$impl->package,'classname'=>$impl->name)}">{$impl->name}</a>
                {else}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$impl->package,'interfacename'=>$impl->name)}">{$impl->name}</a>
                {/if}
            {/foreach}
            </li>
            <li><strong>Inherits from: </strong>
            {if $class->mother_class}
                {if $class->mother_class->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$class->mother_class->package,'interfacename'=>$class->mother_class->name)}">{$class->mother_class->name}</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$class->mother_class->package,'classname'=>$class->mother_class->name)}">{$class->mother_class->name}</a>
                {/if}
            {/if}</li>
            <li><strong>Inherited by: </strong>
            {foreach $relations->descendants as $desc}
                {if $comp->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$desc->package,'interfacename'=>$desc->name)}">{$desc->name}</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$desc->package,'classname'=>$desc->name)}">{$desc->name}</a>
                {/if}
            {/foreach}
            </li>
            <li><strong>Returned by: </strong>
            {foreach $relations->asFunctionReturn as $fct}
            <a href="{jurl 'rarangi_web~components:functiondetails', array('project'=>$project->name,'package'=>$fct->package,'functionname'=>$fct->name)}">{$fct->name}()</a>
            {/foreach}
            {foreach $relations->asMethodReturn as $meth}
                {if $meth->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$meth->package,'interfacename'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$meth->package,'classname'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {/if}
            {/foreach}

            </li>
            <li><strong>As parameter for: </strong>
            {foreach $relations->asFunctionParameter as $fct}
            <a href="{jurl 'rarangi_web~components:functiondetails', array('project'=>$project->name,'package'=>$fct->package,'functionname'=>$fct->name)}">{$fct->name}()</a>
            {/foreach}
            {foreach $relations->asMethodParameter as $meth}
                {if $meth->is_interface}
            <a href="{jurl 'rarangi_web~components:interfacedetails', array('project'=>$project->name,'package'=>$meth->package,'interfacename'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {else}
            <a href="{jurl 'rarangi_web~components:classdetails', array('project'=>$project->name,'package'=>$meth->package,'classname'=>$meth->name)}#m-{$meth->method_name}">{$meth->name}::{$meth->method_name}()</a>
                {/if}
            {/foreach}
            </li>
        </ul>
        </div>
        {if $comp->links || $comp->see || $comp->user_tags}
        <ul class="ra-properties">
            {if $comp->links}
            <li><strong>links: </strong>
                <ul class="ra-links">{foreach $comp->links as $link}
                    <li><a href="{$link[0]|eschtml}">{if $link[1]}{$link[1]|eschtml}{else}{$link[0]|eschtml}{/if}</a></li>{/foreach}
                </ul></li>{/if}
            {if $comp->see}
            <li><strong>{@default.seealso@}: </strong>
                <ul class="ra-see">{foreach $comp->see as $s}
                    <li>{$s|eschtml}</li>{/foreach}
                </ul></li>{/if}
            {if $comp->user_tags}
            {foreach $comp->user_tags as $t=>$c}
            <li><strong>{$t|eschtml}{if $c}: {/if}</strong>{$c|eschtml}</li>
            {/foreach}{/if}
        </ul>
        {/if}
  </div>
{else}
    <h2>Class: {$classname}</h2>
    <div class="ra-block">
        <p>Error, unknow class</p>
    </div>
{/if}
  </div>
