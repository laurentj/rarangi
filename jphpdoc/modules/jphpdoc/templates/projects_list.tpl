	<div class="monbloc">
        <h2>Projects list</h2>
        <div class="blockcontent">
			<ul>
				{foreach $projectslist as $project}
				<li><a href="{jurl 'jphpdoc~default:project', array('project'=>$project->name)}">{$project->name|eschtml}</a></li>
				{/foreach}
			</ul>
        </div>
    </div>
