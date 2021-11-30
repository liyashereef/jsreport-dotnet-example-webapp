<template>
    <div class="feed" ref="feed">
        <ul v-if="contact">
            <li v-for="message in messages" :class="`message${message.to == contact.id ? ' sent' : ' received'}`" :key="message.id">
                <div class="text">
                    {{ message.text }} 
                </div>
                <div class="time">
                    {{ message.created_at | dateformat}} 
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            contact: {
                type: Object
            },
            messages: {
                type: Array,
                required: true
            }
        },
        methods: {
            scrollToBottom() {
                setTimeout(() => {
                    this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
                }, 50);
            }
        },
        watch: {
            contact(contact) {
                this.scrollToBottom();
            },
            messages(messages) {
                this.scrollToBottom();
            }
        },
        filters: {
          dateformat: function (value) {
            if (!value) return ''
            value = value.toString()
            const d = new Date(value);
            let text = d.toDateString().substring(0,8) + d.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) ;
            return text;
          }
        }
    }
</script>

<style lang="scss" scoped>
.feed {
    background: #f0f0f0;
    height: 100%;
    max-height: 470px;
    overflow: scroll;

    ul {
        list-style-type: none;
        padding: 5px;

        li {
            &.message {
                margin: 10px 0;
                width: 100%;

                .text {
                    max-width: 400px;
                    border-radius: 5px;
                    padding: 12px;
                    display: inline-block;
                }

                &.received {
                    text-align: left;

                    .text {
                        background: #d0d7dc;
                    }
                    .time {
                        color: #6d6666;
                        font-size:10px;
                        text-align:left;
                    }
                }

                &.sent {
                    text-align: right;

                    .text {
                           background: #f23520;
                           color: #f8f9fa;
                    }
                    .time {
                        color: #6d6666;
                        font-size:10px;
                        text-align:right !important;
                    }
                }
            }
        }
    }
}
</style>
