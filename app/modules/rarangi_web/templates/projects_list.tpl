<div id="ra-page">
    <h2>{@rarangi_web~default.page.home.title@}</h2>
    <ul id="ra-projects-list">
    {foreach $projectslist as $project}
        <li><a href="{jurl 'rarangi_web~default:project', array('project'=>$project->name)}">{$project->name|eschtml}</a></li>
    {/foreach}
    </ul>
</div>