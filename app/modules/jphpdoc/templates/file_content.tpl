<h2>{$filename}</h2>

{if $file->isdir}

<ul>
{if $file->fullpath != '' }
<li><a href="{jurl 'jphpdoc~sources:index', array('project'=>$project, 'path'=>$file->dirname)}">..</a></li>
{/if}
{foreach $directory as $f}
<li><a href="{jurl 'jphpdoc~sources:index', array('project'=>$project, 'path'=>$f->fullpath)}">{$f->filename}</a></li>
{/foreach}
</ul>

{else}

<ul>
{if $file->dirname != ''}
<li><a href="{jurl 'jphpdoc~sources:index', array('project'=>$project, 'path'=>$file->dirname)}">..</a></li>
{/if}
{foreach $filecontent as $line}
<li><a id="{$line->linenumber}" href="#{$line->linenumber}">{$line->linenumber}</a> <code>{$line->content|eschtml}</code></li>
{/foreach}
</ul>
{/if}