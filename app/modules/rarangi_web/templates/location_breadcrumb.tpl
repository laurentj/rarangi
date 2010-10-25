{meta_html js $j_jquerypath.'jquery.js'}
{meta_html js $j_basepath.'js/jmenulist/jmenulist.js'}
{meta_html css $j_basepath.'js/jmenulist/jmenulist.css'}
<div id="ra-location-breadcrumb">
    <ul id="ra-breadcrumb" class="jmenulist">
        <li><a href="{jurl 'rarangi_web~default:index'}">Projects</a> &gt;
                {if $projectslist && $projectslist->rowCount()}
                <ul>
                    {foreach $projectslist as $p}
                        <li><a href="{jurl 'rarangi_web~default:project', array('project'=>$p->name)}">{$p->name|eschtml}</a></li>
                    {/foreach}</ul>
                {/if}
        </li>
    {if $project}
        <li><a href="{jurl 'rarangi_web~default:project', array('project'=>$project)}">{$project|eschtml}</a> &gt;
            <ul>
                <li><a href="{jurl 'rarangi_web~packages:index', array('project'=>$project)}">{@rarangi_web~default.packages@}</a></li>
                <li><a href="{jurl 'rarangi_web~sources:index', array('project'=>$project)}">{@rarangi_web~default.sources@}</a></li>
                <li><a href="{jurl 'rarangi_web~default:errors', array('project'=>$project)}">Errors</a></li>
            </ul>
        </li>
        {foreach $items as $item}
        <li>{if $item->url}<a href="{$item->url}">{$item->name|eschtml}</a> &gt;{else}{$item->name|eschtml}{/if}
         {if $item->children} <ul> {foreach $item->children as $child}
            <li><a href="{$child->url}">{$child->name|eschtml}</a></li>
         {/foreach}</ul>{/if}
        </li>
        {/foreach}
    {/if}
    </ul>
</div>

<script type="text/javascript">{literal}
     $(document).ready(function(){
          $('#ra-breadcrumb').jmenulist();
     });
{/literal}</script>

