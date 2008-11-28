<div class="monbloc">
{if $project}
        <h2>Project: {$project->name|eschtml}</h2>
        <div class="blockcontent">
			<ul>
				<li><a href="{jurl 'jphpdoc~packages:index', array('project'=>$project->name)}">packages</a></li>
				<li><a href="{jurl 'jphpdoc~sources:index', array('project'=>$project->name)}">Browse source</a></li>
			</ul>
		
			<p>TODO : here displays</p>
			<ul>
				<li>statistics on source codes</li>
				<li>links to static files (VERSION, LICENCE, &co)</li>
				<li>links to archives to download</li>
			</ul>
        </div>
{else}
        <h2>Project: {$projectname}</h2>
        <div class="blockcontent">
			<p>Error, unknow project</p>
        </div>
{/if}
</div>
