<template>
    <div class="chat-app">
        <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
        <ContactsList :contacts="contacts" :newcontact="newcontact"  @selected="startConversationWith"/>
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
        },
        data() {
            return {
                selectedContact: null,
                messages: [],
                contacts: [],
                newcontact:null
            };
        },
        mounted() {
            console.log(this.user.id);
            Echo.channel(`messages.${this.user.id}`)
                .listen('.client-NewMessage', (e) => {
                    console.log("message received");
                    console.log(e);
                    this.hanleIncomingAppMessage(e.message);
                });
            this.getContactList()

            Echo.channel(`updateContact.${this.user.id}`)
                .listen('.contact', (e) => {
                    console.log("inside update contact");
                    this.getContactList(e.newcontact)
                });

        },
        methods: {
            getContactList(newcontact_id){
              axios.get('/chat/contacts')
                  .then((response) => {
                      this.contacts = response.data;
                        this.newcontact = newcontact_id;
                  });
            },
            startConversationWith(contact) {
                this.updateUnreadCount(contact, true);
                 console.log(contact);
                axios.get(`/chat/conversation/${contact.contact_id}`)
                    .then((response) => {
                        this.messages = response.data;
                        this.selectedContact = contact;
                    })
            },
            saveNewMessage(message) {
                      console.log("save message");
                
                this.messages.push(message);
            },
            hanleIncoming(message) {
                console.log("ins handle");
                console.log("msg from"+message.from);
                console.log("selected cont"+this.selectedContact.contact_id);

                if (this.selectedContact && message.from == this.selectedContact.contact_id) {
                    this.saveNewMessage(message);
                    return;
                }

               // this.updateUnreadCount(message.from_contact, false);
            },
             hanleIncomingAppMessage(message) {

                axios.post('/chat/conversation/save', {
                    from: message.from,
                    to: message.to,
                    text: message.text
                }).then((response) => {
                   console.log(response);
                })

                if (this.selectedContact && message.from == this.selectedContact.contact_id) {
                    this.saveNewMessage(message);
                    return;
                }

               // this.updateUnreadCount(message.from_contact, false);
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