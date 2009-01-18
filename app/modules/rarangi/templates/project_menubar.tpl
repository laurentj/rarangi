<div id="menubar-top">
<ul>
    <li><a href="" title="{@rarangi~default.search@}"{if $mode=='search'} class="active"{/if}>{@rarangi~default.search@}</a></li>
    <li><a href="{jurl 'rarangi~default:project', array('project'=>$project->name)}" title="{@rarangi~default.browse@}"{if $mode=='browse'} class="active"{/if}>{@rarangi~default.browse@}</a></li>
</ul>
<span>{$project->name}</span>

</div>
<div id="menubar-content">
{if $mode == 'browse'}
<p>{@rarangi~default.browseby@} 
<span><a href="{jurl 'rarangi~packages:index', array('project'=>$project->name)}">{@rarangi~default.packages@}</a></span>
<span><a href="{jurl 'rarangi~sources:index', array('project'=>$project->name)}">{@rarangi~default.sources@}</a></span>
</p>
{elseif $mode == 'search'}
<input type="text"/><input type="submit" value="Search"/>
{/if}
</div>
