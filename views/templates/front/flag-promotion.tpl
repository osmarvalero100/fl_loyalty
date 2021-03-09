{if !empty($promotions)}
    <div class="loyalty-flag">
        {foreach from=$promotions item=promotion}
            <div class="loyalty-flag-promotion text-center">
                <spam>{$promotion.promotion}</spam>
            </div>
        {/foreach}
    </div>
{/if}