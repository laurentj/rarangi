<div class="monbloc">
{if $project}
        <h2>Project: {$project->name|eschtml}</h2>
        <div class="blockcontent">
	
		<p>TODO : here displays</p>
		<ul>
			<li>list of classes and functions</li>
		</ul>
        </div>
{else}
        <h2>Project: {$projectname}</h2>
        <div class="blockcontent">
		<p>Error, unknow project</p>
        </div>
{/if}
</div>
