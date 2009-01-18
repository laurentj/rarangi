<div id="location-breadcrumb">
    <a href="{jurl 'rarangi~default:index'}" title="{@default.breadcrumb.projects@}">{@default.breadcrumb.projects@}</a> &gt;
    {if $mode =='home'}{@default.breadcrumb.home@}{else}
    <a href="{jurl 'rarangi~default:project',array('project'=>$projectname)}" title="{$projectname|eschtml}">{$projectname|eschtml}</a> &gt;
        {if $mode == 'projecthome'} {@default.breadcrumb.home@}
        {elseif $mode == 'projectbrowse'}{@default.browse@}
        {elseif $mode == 'projectsearch'}{@default.search@}{/if}
    {/if}
</div>
