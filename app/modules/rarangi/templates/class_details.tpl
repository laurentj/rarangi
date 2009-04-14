{if $class}
    <h2>{if $class->is_interface}Interface{else}Class{/if}: {$class->name|eschtml}</h2>
        <div class="block">
        <h3 id="description">{@default.description@}</h3>
        {if $class->mother_class}
        <div class="class-inheriting">
            This {if $class->is_interface}interface{else}class{/if} extends <a href="{jurl 'rarangi~components:classdetails', array('project'=>$project->name,'package'=>$package,'classname'=>$class->mother_class_name)}">{$class->mother_class_name}</a>
        </div>
        {/if}

        {if $class->is_abstract}
        <div class="class-abstract">
            {@default.class.isabstract@}
        </div>
        {/if}
        {if $class->short_description || $class->description}
        <div id="short-description">{$class->short_description|eschtml}</div>
        <div id="text-description">{$class->description|eschtml}</div>
        {else}
        <div id="text-description">{@default.nodescription@}</div>
        {/if}
        <div id="internal-description">{$class->internal|eschtml}</div>
        
        {if $class->see}
        <h4>{@default.seealso@}</h4>
        <ul class="see-links">{foreach $class->see as $see}
            <li>{$see|eschtml}</li>{/foreach}
        </ul>
        {/if}
        </div>
        {if count($properties) || count($methods)}
        <div class="block">
        <h3 id="properties">Short list</h3>
        <div class="class-short-list">
          
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


        <div class="block">
        <h3 id="properties">List of properties</h3>
        <div class="class-properties-list">
            {if count($properties)}
              {foreach $properties as $p}
              <div class="class-property">
              <h4 id="p-{$p->name}"><a name="p-{$p->name}"></a>{$p->name}</h4>
                {if $p->short_description}<div class="short-description">{$p->short_description|eschtml}</div>{/if}
                {if $p->description}<div class="description">{$p->description|eschtml}</div>{/if}
                {if $p->internal}<div class="internal">{$p->internal|eschtml}</div>{/if}
                {if $p->since}<div class="since">Since {$p->since|eschtml}</div>{/if}
                <p class="datatype">Datatype : {if $p->datatype}{$p->datatype}{else}undefined{/if}</p>
                <p class="type-access">{if $p->type == 1}static{elseif $p->type == 2}const{/if}
                   {if $p->accessibility == 'PRO'}protected{elseif $p->accessibility=='PRI'}private{else}public{/if}</p>

                {if $p->copyright}
                <div class="copyright">Copyright: {$p->copyright|eschtml}</div>
                {/if}
                {if $p->license_label || $p->license_text}
                <div class="licence">This property has been added under the licence:
                    {if $p->license_link}
                        <a href="{$class->license_link|eschtml}">{$p->license_label|eschtml}</a>
                    {else}
                        {$p->license_label|eschtml}
                    {/if}
                    {if $p->license_text}
                    <div class="license-description">
                        {$p->license_text|eschtml}
                    </div>
                    {/if}
                </div>
                {/if}
                {if $p->links}
                <ul class="links">{foreach $p->links as $link}
                    <li><a href="{$link[0]|eschtml}">{$link[1]|eschtml}</a></li>{/foreach}
                </ul>
                {/if}
                {if $p->see}
                <ul class="see">{foreach $p->see as $s}
                    <li><a href="{$s[0]|eschtml}">{$s[1]|eschtml}</a></li>{/foreach}
                </ul>
                {/if}
                {if $p->todo}
                <div class="todo">{$p->todo|eschtml}</div>
                {/if}
                {if $p->changelog}
                <div class="changelog">{foreach $p->changelog as $changelog}
                    <div>{$changelog|eschtml}</div>
                    {/foreach}
                </div>
                {/if}
              </div>
              {/foreach}
            {else}
            No properties
            {/if}
        </div>
        </div>
        
        <div class="block">
        <h3 id="methods">List of methods</h3>
        <div class="class-methods-list">
            {if count($methods)}
              {foreach $methods as $p}
              <div class="method-details">
              <h4 id="m-{$p->name}">{$p->name}</h4>
                {if $p->short_description}<div class="short-description">{$p->short_description|eschtml}</div>{/if}
                {if $p->description}<div class="description">{$p->description|eschtml}</div>{/if}
                {if $p->internal}<div class="internal">{$p->internal|eschtml}</div>{/if}
                {if $p->since}<div class="since">Since {$p->since|eschtml}</div>{/if}
                <div class="datatype">Return : {if $p->return_datatype}{$p->return_datatype}{else}void{/if}
                {if $p->return_description}<br/>{$p->return_description}{/if}</div>
                <p class="type-access">{if $p->is_static == 1}static{/if} {if $p->is_final == 1}final{/if} {if $p->is_abstract == 1}abstract{/if}
                   {if $p->accessibility == 'PRO'}protected{elseif $p->accessibility=='PRI'}private{else}public{/if}</p>
                <dl class="parameters">
                    {foreach $method_parameters[$p->name] as $k=>$param}
                    <dt>{$param->type} <strong>${$param->name}</strong> {if $param->defaultvalue}= {$param->defaultvalue}{/if}</dt>
                    <dd>{$param->documentation|eschtml}</dd>
                {/foreach}</dl>

                {if $p->copyright}
                <div class="copyright">Copyright: {$p->copyright|eschtml}</div>
                {/if}
                {if $p->license_label || $p->license_text}
                <div class="licence">This property has been added under the licence:
                    {if $p->license_link}
                        <a href="{$class->license_link|eschtml}">{$p->license_label|eschtml}</a>
                    {else}
                        {$p->license_label|eschtml}
                    {/if}
                    {if $p->license_text}
                    <div class="license-description">
                        {$p->license_text|eschtml}
                    </div>
                    {/if}
                </div>
                {/if}
                {if $p->links}
                <ul class="links">{foreach $p->links as $link}
                    <li><a href="{$link[0]|eschtml}">{$link[1]|eschtml}</a></li>{/foreach}
                </ul>
                {/if}
                {if $p->see}
                <ul class="see">{foreach $p->see as $s}
                    <li><a href="{$s[0]|eschtml}">{$s[1]|eschtml}</a></li>{/foreach}
                </ul>
                {/if}
                {if $p->todo}
                <div class="todo">{$p->todo|eschtml}</div>
                {/if}
                {if $p->changelog}
                <div class="changelog">{foreach $p->changelog as $changelog}
                    <div>{$changelog|eschtml}</div>
                    {/foreach}
                </div>
                {/if}

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
            {if $class->copyright}
            <div class="copyright">Copyright: {$class->copyright|eschtml}</div>
            {/if}
            {if $class->license_label || $class->license_text}
            <div class="licence">This class is available under the licence:
                {if $class->license_link}
                    <a href="{$class->license_link|eschtml}">{$class->license_label|eschtml}</a>
                {else}
                    {$class->license_label|eschtml}
                {/if}
                {if $class->license_text}
                <div class="license-description">
                    {$class->license_text|eschtml}
                </div>
                {/if}
            </div>
            {/if}
            {if $class->links}
            <ul class="links">{foreach $class->links as $link}
                <li><a href="{$link[0]|eschtml}">{$link[1]|eschtml}</a></li>{/foreach}
            </ul>
            {/if}

            {if $class->todo}
            <div class="todo">{$class->todo|eschtml}</div>
            {/if}
            {if $class->changelog}
            <div class="changelog">{foreach $class->changelog as $changelog}
                <div>{$changelog|eschtml}</div>
                {/foreach}
            </div>
            {/if}
        </div>
        </div>
{else}
    <h2>Class: {$classname}</h2>
    <div class="blockcontent">
        <p>Error, unknow class</p>
    </div>
{/if}
