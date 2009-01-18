<h2>{@default.page.home.title@}</h2>
<ul id="projects-list">
{foreach $projectslist as $project}
    <li><a href="{jurl 'rarangi~default:project', array('project'=>$project->name)}">{$project->name|eschtml}</a></li>
{/foreach}
</ul>
