{if $project}
    <h2>{@rarangi_web~default.errors.list@}</h2>
    
    <div class="block" id="errors-criterias">
        <a {if $criteria == ''} class="active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname)}">{@rarangi_web~default.errors.criteria.errors.warnings@}</a>
        <a {if $criteria == 'all'} class="active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'all')}">{@rarangi_web~default.errors.criteria.all@}</a>
        <a {if $criteria == 'error'} class="active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'error')}">{@rarangi_web~default.errors.criteria.errors@}</a>
        <a {if $criteria == 'warning'} class="active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'warning')}">{@rarangi_web~default.errors.criteria.warnings@}</a>
        <a {if $criteria == 'notice'} class="active" {/if}href="{jurl 'rarangi_web~default:errors', array('project'=>$projectname, 'criteria'=>'notice')}">{@rarangi_web~default.errors.criteria.notices@}</a>
    </div>
    
    <div class="block">

    <dl>
        {foreach $errors as $err}
        <dt class="message-{$err->type}">{$err->type}: {$err->message|eschtml}</dt>
        <dd>
            <a href="{jurl 'rarangi_web~sources:index',
                        array('project'=>$projectname, 'path'=>$err->file)}#{$err->line}">
            {$err->file} {$err->line}</a></dd>
        {/foreach}
    </dl>
    </div>

{else}
    <h2>Project: {$projectname}</h2>
    <div class="blockcontent">
        <p>Error, unknow project</p>
    </div>
{/if}
