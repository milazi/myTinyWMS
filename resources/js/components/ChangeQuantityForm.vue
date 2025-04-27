<template>
    <form method="post" v-bind:action="route('article.change_quantity', [article.id])" @submit="submit">
        <h4 class="modal-title">{{ $t('Change stock') }}</h4>
        <div class="row">
            <div class="w-1/2">
                <div class="form-group">
                    <label for="changelogCurrentQuantity" class="form-label">{{ $t('current stock') }}</label>
                    <div class="form-control-static">
                        <span id="changelogCurrentQuantity">{{ article.quantity }}</span>
                        {{ unit }}
                    </div>
                </div>
            </div>
            <div class="w-1/3 col-lg-offset-2">
                <div class="form-group">
                    <label class="form-label">{{ $t('Withdrawal quantity') }}</label>
                    <div class="form-control-static">
                        <span>{{ article.issue_quantity }}</span>
                        {{ unit }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="w-1/2">
                <div class="form-group">
                    <label class="form-label">{{ $t('Change') }}</label>

                    <div class="flex">
                        <select v-model="changelogChangeType" name="changelogChangeType" id="changelogChangeType">
                            <option value="add">{{ $t('Plus') }}</option>
                            <option value="sub">{{ $t('Minus') }}</option>
                        </select>
                        <input class="form-input w-24 ml-2" type="text" v-model="change" value="" name="changelogChange" id="changelogChange" :placeholder="$t('Quantity')" required>
                    </div>
                </div>
            </div>
            <div class="w-1/2">
                <div class="form-group">
                    <label for="changelogType" class="form-label">{{ $t('Type of change') }}</label>
                    <input type="hidden" name="changelogType" v-model="changelogType.value" />
                    <select id="changelogType" class="form-control" required v-model="changelogType">
                        <option value="" selected disabled></option>
                        <option v-for="item in changeTypes" v-bind:value="item" v-if="(item.ifOnly == '' || changelogChangeType == item.ifOnly)">{{ item.text }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group" v-if="(typeof article.delivery_notes == 'string' && article.delivery_notes.length > 0 && changelogType.value == 1)">
            <label for="deliveryNotes" class="form-label text-red-400">{{ $t('Delivery/GR notes') }}</label>
            <div class="form-control-static" id="deliveryNotes">{{ article.delivery_notes }}</div>
        </div>

        <div class="form-group">
            <label for="changelogNote" class="form-label">{{ $t('Comment') }}</label>
            <textarea class="form-textarea" rows="3" id="changelogNote" name="changelogNote"></textarea>
        </div>

        <div class="text-sm text-red-500" v-if="article.article_group_items.length > 0">
            <span v-if="article.article_group_items.length == 1">{{ $t('This article is included in the following article group. If you want to change the stock of the whole group, please do so directly via the group:') }}</span>
            <span v-if="article.article_group_items.length > 1">{{ $t('This article is included in the following article groups. If you want to change the stock of the whole group, please do so directly via the group:') }}</span>
            <ul class="list-disc pl-4 mt-2">
                <li v-for="(groupItem, index) in article.article_group_items.slice(0, 10)">
                    <a :href="route('article-group.show', [groupItem.article_group_id])">{{ groupItem.article_group.name }}</a>
                </li>
            </ul>
            <span v-if="article.article_group_items.length > 10">...</span>
        </div>

        <div class="modal-footer">
            <input type="hidden" v-bind:value="csrf" name="_token" />
            <button type="button" class="btn btn-default" @click="$modal.hide('change-quantity')">{{ $t('Cancel') }}</button>
            <button type="submit" class="btn btn-primary" id="submitChangeQuantity">{{ $t('Save') }}</button>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['article', 'unit'],

        data() {
            return {
                changelogChangeType: 'sub',
                changelogType: {value: ''},
                change: '',
                csrf: "",
                changeTypes: [
                    {value: 1, text: this.$t('Goods receipt'), ifOnly: 'add'},
                    {value: 2, text: this.$t('Goods issue'), ifOnly: 'sub'},
                    {value: 7, text: this.$t('Inventory'), ifOnly: ''},
                    {value: 8, text: this.$t('Replacement delivery'), ifOnly: ''},
                    {value: 9, text: this.$t('Goods in/out external warehouse'), ifOnly: ''},
                    {value: 10, text: this.$t('Sale to third parties'), ifOnly: 'sub'},
                    {value: 11, text: this.$t('Transfer'), ifOnly: ''},
                ]
            }
        },

        methods: {
            submit(e) {
                if (this.changelogChangeType == 'sub' && this.change > this.article.quantity) {
                    alert(this.$t('It is not possible to book out more than the current stock!'));
                    e.preventDefault();
                    return false;
                }

                let message = this.$t('You want to change the stock by ');
                message += (this.changelogChangeType === 'sub') ? this.$t('MINUS') + ' ' : this.$t('PLUS') + ' ';
                message += this.change + ' ' + this.$t(' - as') + ' ';
                message += '"' + this.changelogType.text + '". ' + this.$t('ARE YOU SURE?');

                if (!confirm(message)) {
                    e.preventDefault();
                }
            }
        },

        mounted() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
        }
    }
</script>