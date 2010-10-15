<div id="ra-page">
{if $project}
    <h2>{@rarangi_web~default.errors.list@}</h2>
    
    <div class="ra-block" id="ra-errors-criterias">
        <a {if $criteria == ''} class="ra-active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname)}">{@rarangi_web~default.errors.criteria.errors.warnings@}</a>
        <a {if $criteria == 'all'} class="ra-active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'all')}">{@rarangi_web~default.errors.criteria.all@}</a>
        <a {if $criteria == 'error'} class="ra-active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'error')}">{@rarangi_web~default.errors.criteria.errors@}</a>
        <a {if $criteria == 'warning'} class="ra-active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'warning')}">{@rarangi_web~default.errors.criteria.warnings@}</a>
        <a {if $criteria == 'notice'} class="ra-active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'notice')}">{@rarangi_web~default.errors.criteria.notices@}</a>
    </div>
    
    <div class="ra-block">
        <dl>
            {foreach $errors as $err}
            <dt class="ra-message-{$err->type}">{$err->type}: {$err->message|eschtml}</dt>
            <dd>
                <a href="{jurl 'rarangi_web~sources:index',
                            array('project'=>$projectname, 'path'=>$err->file)}#{$err->line}">
                {$err->file} {$err->line}</a></dd>
            {/foreach}
        </dl>
    </div>

{else}
    <h2>Project: {$projectname}</h2>
    <div class="ra-block">
        <p>Error, unknow project</p>
    </div>
{/if}
</div>