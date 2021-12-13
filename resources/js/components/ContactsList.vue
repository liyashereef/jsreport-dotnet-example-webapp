<template>
    <div class="contacts-list">
        <ul>
            <li v-for="contact in sortedContacts" :key="contact.contact_id" @click="selectContact(contact)" :class="{ 'selected': contact == selected }">
                <div class="avatar">
                <div class="default-image" v-if="contact.imagepath.length < 3"><label style="padding: 5px 6px;">{{contact.imagepath}}</label></div>
                <img v-else name="image" :src="'../images/uploads/' + contact.imagepath"  class="profileImage">   
                </div>
                <div class="contact">
                    <p class="name">{{ contact.contact[0].full_name }}</p>
                    <p class="email">{{ contact.contact[0].email }}</p>
                </div>
                <span class="unread" v-if="contact.unread">{{ contact.unread }}</span>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            contacts: {
                type: Array,
                default: []
            }
        },
        data() {
            return {
                selected: this.contacts.length ? this.contacts[0] : null,

            };
        },
        methods: {
            selectContact(contact) {
                this.selected = contact;

                this.$emit('selected', contact);
            },
        },
        computed: {
            sortedContacts() {
                return _.sortBy(this.contacts, [(contact) => {
                    if (contact == this.selected) {
                        return Infinity;
                    }

                    return contact.unread;
                }]).reverse();
            }
        }
    }
</script>

<style lang="scss" scoped>

.contacts-list {
    flex: 2;
    max-height: 100%;
    height: 600px;
    overflow: scroll;
    border-left: 1px solid #a6a6a6;

    ul {
        list-style-type: none;
        padding-left: 0;

        li {
            display: flex;
            padding: 2px;
            border-bottom: 1px solid #aaaaaa;
            height: 80px;
            position: relative;
            cursor: pointer;

            &.selected {
                background: #dfdfdf;
            }

            span.unread {
                background: #f35804;
                color: #fff;
                position: absolute;
                right: 11px;
                top: 20px;
                display: flex;
                font-weight: 700;
                min-width: 20px;
                justify-content: center;
                align-items: center;
                line-height: 20px;
                font-size: 12px;
                padding: 0 4px;
                border-radius: 3px;
            }
            .avatar {
                flex: 1;
                display: flex;
                align-items: center;

                img {
                   width: 53px;
                    border-radius: 50%;
                    margin: 0 auto;
                }
               .default-image{
                 width: 52px;
                 height: 52px;
                 border-radius: 50%;
                 background: #f35804;
                 font-size: 25px;
                 color: #fff;
                 text-align: center;
                  margin: 0 auto;
               }
            }
            .contact {
                flex: 3;
                font-size: 10px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                justify-content: center;

                p {
                    margin: 0;

                    &.name {
                        font-weight: bold;
                    }
                }
            }
        }
    }
}
</style>