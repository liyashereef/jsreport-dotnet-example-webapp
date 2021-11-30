<template>
    <div class="chat-app">
        <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
        <ContactsList :contacts="contacts" @selected="startConversationWith"/>
    </div>
</template>

<script>

    import Conversation from './Conversation';
    import ContactsList from './ContactsList';

    export default {
        props: {
            user: {
                type: Object,
                required: true
            },
            newcontact:{
                type: Object,
                required: false
            }
        },
        data() {
            return {
                selectedContact: null,
                messages: [],
                contacts: []
            };
        },
        mounted() {
            console.log(this.user.id);
            Echo.channel(`messages.${this.user.id}`)
                .listen('client-NewMessage', (e) => {
                    console.log("inssssssssssss");
                    console.log(e);
                    this.hanleIncoming(e.message);
                });
            this.getContactList()

        },
        methods: {
            getContactList(){
              axios.get('/chat/contacts')
                  .then((response) => {
                      console.log("ppppppp");
                      this.contacts = response.data;
                  });
            },
            startConversationWith(contact) {
                this.updateUnreadCount(contact, true);

                axios.get(`/chat/conversation/${contact.contact_id}`)
                    .then((response) => {
                        this.messages = response.data;
                        this.selectedContact = contact;
                    })
            },
            saveNewMessage(message) {
                this.messages.push(message);
            },
            hanleIncoming(message) {
                console.log("ins handle");
                if (this.selectedContact && message.from == this.selectedContact.contact_id) {
                    this.saveNewMessage(message);
                    return;
                }

                this.updateUnreadCount(message.from_contact, false);
            },
            updateUnreadCount(contact, reset) {
                console.log(contact);
                this.contacts = this.contacts.map((single) => {
                    if (single.contact_id !== contact.contact_id) {
                        return single;
                    }

                    if (reset)
                        single.unread = 0;
                    else
                        single.unread += 1;

                    return single;
                })
            }
        },
        components: {Conversation, ContactsList}
    }
</script>


<style lang="scss" scoped>
.chat-app {
    display: flex;
    border-color: #d5dde4;
    border-style: solid;
}
</style>