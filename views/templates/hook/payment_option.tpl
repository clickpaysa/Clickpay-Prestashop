{foreach $payment_options as $option}
<div class="row">
    <div class="col-xs-12 col-md-6">
        <p class="payment_module">
            <a title="{l s='Pay by Clickpay' mod='clickpay'}" href="{$option['action']}">
                {$option['title']}
                <img src="{$option['logo']}">
            </a>
        </p>
    </div>
</div>
{/foreach}