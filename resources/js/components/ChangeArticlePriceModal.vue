<template>
    <modal :name="name" height="auto" classes="modal">
        <h4 class="modal-title text-red-500 font-bold text-xl">{{ $t('Price change!') }}</h4>

        <div class="row">
            <div class="w-full">
                <div class="text-base text-gray-800 tracking-tight" v-if="order !== false">
                    {{ $t('The price of at least one article in this order deviates from the current article price.') }}<br>
                    {{ $t('Should the article price be adjusted?') }}
                </div>
                <div class="text-base text-gray-800 tracking-tight" v-if="order === false">
                    {{ $t('The price of this article in this order deviates from the current article price.') }}<br>
                    {{ $t('Should the article price be adjusted?') }}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" @click="submit(0)">{{ $t('No, do not change the article price') }}</button>
            <button type="button" class="btn btn-primary" @click="submit(1)">{{ $t('Yes, adjust the article price') }}</button>
        </div>
    </modal>

</template>

<script>
    import axios from 'axios';

    export default {
        props: {name, status, orderitem: {default: false}, order: {default: false}},

        methods: {
            submit(change_article_price) {
                if (parseInt(this.order) > 0) {
                    axios.post(route('order.all_items_invoice_received', {order: this.order}), {
                        change_article_price: change_article_price
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    axios.post(route('order.item_invoice_received', {orderitem: this.orderitem}), {
                        invoice_status: this.status,
                        change_article_price: change_article_price
                    }).then(function () {
                        location.reload();
                    });
                }
            }
        }
    }
</script>