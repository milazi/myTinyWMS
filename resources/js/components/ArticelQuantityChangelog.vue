<template>
    <div>
        <table class="dataTable table-condensed" id="articleQuantityChangelogTable">
            <thead>
                <tr>
                    <th>{{ $t('Type') }}</th>
                    <th class="text-center">{{ $t('Change') }}</th>
                    <th class="text-center">{{ $t('Stock') }}</th>
                    <th class="text-center">{{ $t('Unit') }}</th>
                    <th>{{ $t('Timestamp') }}</th>
                    <th>{{ $t('Comment') }}</th>
                    <th>{{ $t('User') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in items" v-bind:class="{ 'border-thick': (item.id === highlightedRowId) }">
                    <template v-if="item.type === CHANGELOG_TYPE_INCOMING">
                        <td class="bg-success text-center text-white">{{ $t('GR') }}</td>
                        <td class="text-success text-center">+{{ item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_OUTGOING">
                        <td class="bg-danger text-center text-white">{{ $t('GI') }}</td>
                        <td class="text-danger text-center">{{ item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_CORRECTION">
                        <td class="bg-info text-center text-white">
                            <span v-on:mouseover="highlightedRowId = item.related.id" v-on:mouseout="highlightedRowId = null" data-toggle="tooltip" data-placement="left" v-bind:title="getCorrectionTooltip(item)">{{ $t('CO') }}</span>
                        </td>
                        <td class="text-info text-center">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_COMMENT">
                        <td class="bg-default text-center text-white">{{ $t('CM') }}</td>
                        <td class="text-info text-center"></td>
                        <td class="text-center"></td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_INVENTORY">
                        <td class="bg-primary text-center text-white" :title="$t('Inventory')">{{ $t('INV') }}</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_REPLACEMENT_DELIVERY">
                        <td class="bg-default text-center text-white" :title="$t('Replacement Delivery')">{{ $t('RD') }}</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }"><i>{{ item.change >= 0 ? '+'+item.change : item.change }}</i></td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_OUTSOURCING">
                        <td class="bg-default text-center text-white" :title="$t('External Warehouse')">{{ $t('EW') }}</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }"><i>{{ item.change >= 0 ? '+'+item.change : item.change }}</i></td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_SALE_TO_THIRD_PARTIES">
                        <td class="bg-danger text-center text-white" :title="$t('Sale to Third Parties')">{{ $t('STP') }}</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_TRANSFER">
                        <td class="bg-default text-center" :title="$t('Transfer')">{{ $t('TR') }}</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <template v-if="item.type === CHANGELOG_TYPE_RETOURE">
                        <td class="bg-default text-center" :title="$t('Return')">{{ $t('RT') }}</td>
                        <td class="text-center" v-bind:class="{ 'text-success': (item.change >= 0), 'text-danger': (item.change < 0) }">{{ item.change >= 0 ? '+'+item.change : item.change }}</td>
                        <td class="text-center">{{ item.new_quantity }}</td>
                    </template>

                    <td class="text-nowrap text-center">{{ item.unit ? item.unit.name : ''}}</td>
                    <td class="text-nowrap">{{ item.created_at | moment('MM/DD/YYYY HH:mm') }} {{ $t('o\'clock') }}</td>
                    <td>
                        <a v-if="item.delivery_item" v-bind:href="route('order.show', item.delivery_item.delivery.order)" target="_blank">{{ item.note ? item.note : 'Order ' + item.delivery_item.delivery.order.internal_order_number }}</a>
                        <template v-else>
                            {{ item.note }}
                        </template>
                    </td>
                    <td>{{ item.user ? item.user.name : '' }}</td>
                    <td>
                        <dot-menu class="ml-2" v-if="editEnabled">
                            <a href="#" @click="showChangeNoteDialog(item)">{{ $t('Edit comment') }}</a>
                            <a v-if="itemIsFromCurrentMonth(item, index)" v-bind:href="route('article.quantity_changelog.delete', [article.id, item.id])" v-bind:onclick="getDeleteOnClick(item)">{{ $t('Delete entry') }}</a>
                            <a v-if="item.type === CHANGELOG_TYPE_OUTGOING || item.type === CHANGELOG_TYPE_INCOMING" href="#" @click="showFixChangeNoteDialog(item)">{{ $t('Correct entry') }}</a>
                        </dot-menu>
                    </td>
                </tr>
            </tbody>
        </table>

        <modal name="modal-change-changelog-note"height="auto" classes="modal">
            <h4 class="modal-title">{{ $t('Edit comment') }}</h4>
            <form>
                <div class="form-group">
                    <label for="changelog_note" class="form-label">{{ $t('Comment') }}</label>
                    <input type="text" class="form-input" maxlength="191" name="changelog_note" id="changelog_note" v-model="changeNoteDialogItem.note">
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn" @click="$modal.hide('modal-change-changelog-note')">{{ $t('Cancel') }}</button>
                <button class="btn btn-primary" @click="saveChangelogNote()">{{ $t('Save') }}</button>
            </div>
        </modal>

        <modal name="modal-fix-changelog"height="auto" classes="modal">
            <h4 class="modal-title">{{ $t('Correct stock change') }}</h4>
            <form method="post" v-bind:action="route('article.fix_quantity_change', article.id)" id="fixChangelogForm">
                <div class="row">
                    <div class="w-full">
                        <div class="form-group">
                            <label class="form-label">{{ $t('change to be corrected') }}</label>
                            <div class="form-control-static">
                                <span class="label" v-bind:class="{ 'label-success': (fixChangelogDialogItem.type === CHANGELOG_TYPE_INCOMING), 'label-danger': (fixChangelogDialogItem.type === CHANGELOG_TYPE_OUTGOING) }">
                                    {{ fixChangelogDialogItem.type == CHANGELOG_TYPE_INCOMING ? $t('GI') : $t('GR') }}
                                </span>
                                &nbsp;
                                <span class="bold">{{ fixChangelogDialogItem.change }} {{ fixChangelogDialogItem.unit ? fixChangelogDialogItem.unit.name : '' }}</span>
                                &nbsp;{{ $t('on') }}&nbsp;
                                <span>{{ fixChangelogDialogItem.created_at | moment('MM/DD/YYYY HH:mm') }}</span>
                                &nbsp;{{ $t('by') }}&nbsp;
                                <span>{{ fixChangelogDialogItem.user.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="w-1/2">
                        <div class="form-group">
                            <label for="changelogChange" class="form-label">{{ $t('Change') }}</label>
                            <div class="flex">
                                <select v-model="fixChangelogDialogChangeType" name="changelogChangeType">
                                    <option value="add">{{ $t('Plus') }}</option>
                                    <option value="sub">{{ $t('Minus') }}</option>
                                </select>
                                <input class="form-input w-24 ml-2" type="text" id="changelogChange" value="" name="changelogChange" :placeholder="$t('Quantity')" required>
                            </div>
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="form-group">
                            <label for="changelogFixType" class="form-label">{{ $t('Type of change') }}</label>
                            <select id="changelogFixType" name="changelogType" class="form-select" required>
                                <option v-bind:value="CHANGELOG_TYPE_CORRECTION" data-type="fix">{{ $t('Correction') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="changelogNote" class="form-label">{{ $t('Note') }}</label>
                    <textarea class="form-input" rows="3" id="changelogNote" name="changelogNote"></textarea>
                </div>

                <input type="hidden" v-bind:value="csrf" name="_token" />
                <input type="hidden" name="changelogRelatedId" id="changelogRelatedId" v-bind:value="fixChangelogDialogItem.id" />
            </form>
            <div class="modal-footer">
                <button class="btn" @click="$modal.hide('modal-fix-changelog')">{{ $t('Cancel') }}</button>
                <button class="btn btn-primary" @click="submitChangelogFixForm()">{{ $t('Save') }}</button>
            </div>
        </modal>
    </div>
</template>

<script>
    export default {
        props: ['article', 'items', 'editEnabled'],

        methods: {
            getDeleteOnClick(item) {
                return (item.deliveryItem) ? "return confirm('" + this.$t('Warning, this entry will also be deleted from the associated delivery!') + "')" : '';
            },
            itemIsFromCurrentMonth(item, index) {
                return (moment().format('YYYY-MM') === moment(item.created_at).format('YYYY-MM') && index === 0);
            },
            showFixChangeNoteDialog(item) {
                this.fixChangelogDialogItem = item;
                this.$modal.show('modal-fix-changelog');
            },
            submitChangelogFixForm() {
                document.getElementById("fixChangelogForm").submit();
            },
            showChangeNoteDialog(item) {
                this.changeNoteDialogItem = item;
                this.$modal.show('modal-change-changelog-note');
            },
            saveChangelogNote() {
                let that = this;

                $.post(route('article.change_changelog_note', that.article.id), {content: that.changeNoteDialogItem.note, id: that.changeNoteDialogItem.id}).done(function (data) {
                    that.$modal.hide('modal-change-changelog-note');
                });
            },
            getCorrectionTooltip(item) {
                if (item.related) {
                    var tooltip = this.$t('Correction for') + ' ';
                    tooltip += (item.related.type === this.CHANGELOG_TYPE_INCOMING) ? this.$t('GR') : this.$t('GI');
                    tooltip += ' ' + this.$t('from') + ' ' + moment(item.related.created_at).format('MM/DD/YYYY HH:mm') + ' ' + this.$t('o\'clock');
                    return tooltip;
                }
            }
        },

        data() {
            return {
                fixChangelogDialogChangeType: 'sub',
                fixChangelogDialogItem: {user: {}, created_at: new Date()},

                changeNoteDialogItem: {id: null, note: null},

                csrf: "",

                CHANGELOG_TYPE_INCOMING: 1,
                CHANGELOG_TYPE_OUTGOING: 2,
                CHANGELOG_TYPE_CORRECTION: 3,
                CHANGELOG_TYPE_COMMENT: 6,
                CHANGELOG_TYPE_INVENTORY: 7,
                CHANGELOG_TYPE_REPLACEMENT_DELIVERY: 8,
                CHANGELOG_TYPE_OUTSOURCING: 9,
                CHANGELOG_TYPE_SALE_TO_THIRD_PARTIES: 10,
                CHANGELOG_TYPE_TRANSFER: 11,
                CHANGELOG_TYPE_RETOURE: 12,

                highlightedRowId: null
            }
        },

        mounted() {
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content
        }
    }
</script>