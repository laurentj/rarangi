<!--<div id="menubar-top">
<ul>
    <li><a href="" title="{@rarangi_web~default.search@}"{if $mode=='search'} class="active"{/if}>{@rarangi_web~default.search@}</a></li>
    <li><a href="{jurl 'rarangi_web~default:project', array('project'=>$project->name)}" title="{@rarangi_web~default.browse@}"{if $mode=='browse'} class="active"{/if}>{@rarangi_web~default.browse@}</a></li>
</ul>
<span>{$project->name}</span>

</div>
<div id="menubar-content">
{if $mode == 'browse'}
<p>{@rarangi_web~default.browseby@} 
<span><a href="{jurl 'rarangi_web~packages:index', array('project'=>$project->name)}">{@rarangi_web~default.packages@}</a></span>
<span><a href="{jurl 'rarangi_web~sources:index', array('project'=>$project->name)}">{@rarangi_web~default.sources@}</a></span>
</p>
{elseif $mode == 'search'}
<input type="text"/><input type="submit" value="Search"/>
{/if}
</div>-->
