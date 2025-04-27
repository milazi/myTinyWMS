<template>
    <div class="row flex order-messages">
        <div class="tabs border-r">
            <div class="flex flex-col py-2 pr-4 pl-2 border-b" v-for="(message, index) in messages" :key="index" :class="currentIndex == index ? 'active' : ''" @click="currentIndex = index">
                <div class="flex">
                    <div class="w-1/2 font-bold text-sm" :title="message.user ? message.user.name : ''">
                        {{ getSenderName(message) }}
                    </div>
                    <div class="w-1/2 text-xs text-gray-500 text-right" :title="message.received | moment('MM/DD/YYYY HH:mm:SS')">
                        <z icon="attachment" class="fill-current w-3 h-3 inline-block" v-if="Object.keys(message.attachments).length"></z>
                        {{ message.received | moment('MM/DD/YYYY') }}
                    </div>
                </div>
                <div class="text-sm mt-4">
                    {{ message.subject }}
                    <span class="label label-primary" v-if="!message.read">{{ $t('NEW') }}</span>
                </div>
            </div>
        </div>
        <div class="flex-1 px-4" v-if="messages.length">
            <div class="flex flex-col py-2 pr-4 border-b">
                <div class="flex">
                    <div class="text-xs text-gray-500 flex-1">
                        <z icon="time" class="fill-current w-3 h-3 inline-block"></z> {{ messages[currentIndex].received | moment('dddd, MM/DD/YY, HH:mm ') }}{{ $t('o\'clock') }}
                        <template v-if="messages[currentIndex].sender.includes('System')">
                            {{ $t('from') }} {{ messages[currentIndex].user ? messages[currentIndex].user.name : 'System' }} {{ $t('to') }} {{ messages[currentIndex].receiver.join(', ') }} (#{{ messages[currentIndex].id }})
                        </template>
                        <template v-else>
                            {{ $t('from') }} {{ messages[currentIndex].sender.join(', ') }} (#{{ messages[currentIndex].id }})
                        </template>
                    </div>
                    <dot-menu class="order-message-menu" v-if="editEnabled">
                        <a :href="route('order.message_forward_form', [messages[currentIndex]])" :title="$t('Forward')"><i class="fa fa-forward"></i> {{ $t('Forward') }}</a>
                        <template v-if="order">
                        <a :href="route('order.message_create', {'order': order, 'answer': messages[currentIndex].id})"><i class="fa fa-reply"></i> {{ $t('Reply') }}</a>

                        <a :href="route('order.message_read', [order, messages[currentIndex]])" title="Mark as Read" v-if="!messages[currentIndex].read"><i class="fa fa-eye"></i> {{ $t('Read') }}</a>
                        <a :href="route('order.message_unread', [order, messages[currentIndex]])" title="Mark as Unread" v-else><i class="fa fa-eye"></i> {{ $t('Unread') }}</a>
                        </template>
                        <a :title="$t('Move to order')" @click.prevent="$modal.show('assignOrderMessageModal', {message_id: messages[currentIndex].id })"><i class="fa fa-share"></i> {{ $t('Move') }}</a>
                        <a :href="route('order.message_delete', {'message': messages[currentIndex], 'order': order})" :onclick="'return confirm(\'' + $t('Really delete?') + '\')'" :title="$t('Delete message')"><i class="fa fa-trash-o"></i> {{ $t('Delete') }}</a>
                    </dot-menu>
                </div>

                <h1 class="my-2 pm-2 border-b">{{ messages[currentIndex].subject }}</h1>

                <div class="border-b pb-2 flex flex-wrap" v-if="Object.keys(messages[currentIndex].attachments).length">
                    <a v-for="(attachment, index) in messages[currentIndex].attachments" :href="route('order.message_attachment_download', [messages[currentIndex].id, attachment.fileName])" class="block border flex items-center p-4 mb-2 mr-4 hover:bg-gray-400 rounded">
                        <z icon="document" class="fill-current w-4 h-4 mr-2"></z>
                        <div class="text-sm">{{ attachment.orgFileName }}</div>
                    </a>
                </div>

                <iframe seamless frameborder="0" class="w-full h-screen" :srcdoc="messages[currentIndex].htmlBody" v-if="messages[currentIndex].htmlBody != '' && messages[currentIndex].htmlBody != 0"></iframe>
                <div class="w-full h-screen" v-else v-html="cleanTextBody(messages[currentIndex].textBody)"></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        props: ['messages', 'order', 'editEnabled'],

        data() {
            return {
                currentIndex: 0,
            }
        },

        methods: {
            cleanTextBody(content) {
                return this.nl2br(this.stripTags(content));
            },
            stripTags(content) {
                let regex = /(<([^>]+)>)/ig;

                return content.replace(regex, "");
            },
            nl2br (str) {
                // Some latest browsers when str is null return and unexpected null value
                if (typeof str === 'undefined' || str === null) {
                    return '';
                }

                let breakTag = '<br>';

                return (str + '').replace(/(\r\n|\n\r|\r|\n)/g, breakTag + '$1');
            },
            getSenderName(message) {
                return message.sender.includes('System') ? 'System' : this.$t('Supplier')
            }
        }
    }
</script>