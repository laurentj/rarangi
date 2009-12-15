{if $func}
    <h2>Function: {$func->name|eschtml}</h2>
    <div class="block">

    </div>
{else}
    <h2>Class: {$classname}</h2>
    <div class="blockcontent">
        <p>Error, unknow class</p>
    </div>
{/if}
