<h2>{$filename}</h2>

{if $file->isdir}

<ul>
{if $file->fullpath != '' }
<li><a href="{jurl 'rarangi_web~sources:index', array('project'=>$project, 'path'=>$file->dirname)}">..</a></li>
{/if}
{foreach $directory as $f}
<li><a href="{jurl 'rarangi_web~sources:index', array('project'=>$project, 'path'=>$f->fullpath)}">{$f->filename}</a></li>
{/foreach}
</ul>

{else}
{if $file->dirname != ''}
<p><a href="{jurl 'rarangi_web~sources:index', array('project'=>$project, 'path'=>$file->dirname)}">Up to {$file->dirname|eschtml}</a></p>
{/if}

<h3>File content</h3>
<div class="file-content">
<ul>
{foreach $filecontent as $line}
<li><a id="{$line->linenumber}" href="#{$line->linenumber}">{$line->linenumber}</a> <code>{$line->content|eschtml}</code></li>
{/foreach}
</ul>
</div>
{/if}
