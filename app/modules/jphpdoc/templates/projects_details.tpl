	<div class="monbloc">
		{if $project}
        <h2>Project: {$project->name|eschtml}</h2>
        <div class="blockcontent">
			<ul>
				<li><a href="#">packages</a></li>
				<li><a href="#">Browse source</a></li>
			</ul>
		
			<p>TODO : here displays</p>
			<ul>
				<li>statistics on source codes</li>
				<li>links to static files (VERSION, LICENCE, &co)</li>
			</ul>
        </div>
		{else}
        <h2>Project: {$projectname}</h2>
        <div class="blockcontent">
			<p>Error, unknow project</p>
        </div>
		{/if}
    </div>
