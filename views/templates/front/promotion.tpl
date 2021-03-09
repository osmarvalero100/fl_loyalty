{if !empty($promotions)}
    <div class="loyalty-description">
        {foreach from=$promotions item=promotion}
            <div class="alert alert-success">
                <spam>{$promotion.description nofilter}</spam>
            </div>
        {/foreach}
    </div>
{/if}