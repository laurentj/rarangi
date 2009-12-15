<div id="location-breadcrumb">
    <a href="{jurl 'rarangi_web~default:index'}" title="{@rarangi_web~default.breadcrumb.projects@}">{@rarangi_web~default.breadcrumb.projects@}</a> &gt;
    {if $mode =='home'}{@rarangi_web~default.breadcrumb.home@}{else}
    <a href="{jurl 'rarangi_web~default:project',array('project'=>$projectname)}" title="{$projectname|eschtml}">{$projectname|eschtml}</a> &gt;
        {if $mode == 'projecthome'} {@rarangi_web~default.breadcrumb.home@}
        {elseif $mode == 'projectbrowse'}{@rarangi_web~default.browse@}
        {elseif $mode == 'projectsearch'}{@rarangi_web~default.search@}{/if}
    {/if}
</div>
