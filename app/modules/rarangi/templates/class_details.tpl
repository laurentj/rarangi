<div class="monbloc">
{if $class}
    <h2>{if $class->is_interface}Interface{else}Class{/if}: {$class->name|eschtml}</h2>
    <div class="blockcontent">
        <h3 id="description">Description</h3>
        {if $class->mother_class}
        <div class="class-inheriting">
            This {if $class->is_interface}interface{else}class{/if} extends <a href="{jurl 'rarangi~components:classdetails', array('project'=>$project->name,'package'=>$package,'classname'=>$class->mother_class_name)}">{$class->mother_class_name}</a>
        </div>
        {/if}

        {if $class->is_abstract}
        <div class="class-abstract">
            This is an abstract class.
        </div>
        {/if}
        {if $class->short_description || $class->description}
        <div id="short-description">{$class->short_description|eschtml}</div>
        <div id="text-description">{$class->description|eschtml}</div>
        {else}
        <div id="text-description">No description</div>
        {/if}
        <div id="internal-description">{$class->internal|eschtml}</div>
        
        {if $class->see}
        <h4>See also</h4>
        <ul class="see-links">{foreach $class->see as $see}
            <li>{$see|eschtml}</li>{/foreach}
        </ul>
        {/if}

        <h3 id="properties">List of properties</h3>
        <div class="class-properties-list">
            TODO list of properties here
        </div>
        
        <h3 id="methods">List of methods</h3>
        <div class="class-methods-list">
            TODO list of methods here
        </div>

        <h3 id="relation">Relation to other components</h3>
        <ul>
            <li>TODO: list of methods/functions which return this class</li>
            <li>TODO: list of methods/functions which accept this class as parameter</li>
            <li>TODO: list of classes which inherits from this class</li>
        </ul>

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
</div>
