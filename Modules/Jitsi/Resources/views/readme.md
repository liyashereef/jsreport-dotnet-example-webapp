https://github.com/mjtiempo/Docs/blob/master/Jitsi%20Meet%20Installation.md

cd &&
apt-get update -y &&
apt-get install gcc -y &&
apt-get install unzip -y &&
apt-get install lua5.2 -y &&
apt-get install liblua5.2 -y &&
apt-get install luarocks -y &&
luarocks install basexx &&
apt-get install libssl-dev -y &&
luarocks install luacrypto2 &&
mkdir src &&
cd src &&
luarocks download lua-cjson &&
luarocks unpack lua-cjson-2.1.0.6-1.src.rock &&
cd lua-cjson-2.1.0.6-1/lua-cjson &&
sed -i 's/lua_objlen/lua_rawlen/g' lua_cjson.c &&
sed -i 's|$(PREFIX)/include|/usr/include/lua5.2|g' Makefile &&
luarocks make &&
cd &&
apt install git cmake liblua5.1-0-dev -y &&
git clone https://github.com/evanlabs/luacrypto &&
cd luacrypto &&
luarocks make &&
cd &&
luarocks install luajwtjitsi &&
cd &&
wget https://prosody.im/files/prosody-debian-packages.key -O- | sudo apt-key add - &&
echo deb http://packages.prosody.im/debian $(lsb_release -sc) main | sudo tee -a /etc/apt/sources.list &&
apt-get update -y &&
apt-get upgrade -y &&
apt-get install prosody -y &&
chown root:prosody /etc/prosody/certs/localhost.key &&
chmod 644 /etc/prosody/certs/localhost.key &&
sleep 2 &&
shutdown -r now

cd &&
cp /etc/prosody/certs/localhost.key /etc/ssl &&
apt-get install nginx -y &&
wget -qO - https://download.jitsi.org/jitsi-key.gpg.key | sudo apt-key add - &&
sh -c "echo 'deb https://download.jitsi.org stable/' > /etc/apt/sources.list.d/jitsi-stable.list" &&
apt-get -y update &&
apt-get install jitsi-meet -y &&
apt-get install jitsi-meet-tokens -y


/usr/share/jitsi-meet/scripts/install-letsencrypt-cert.sh

ufw enable

ufw allow in 22/tcp &&
ufw allow in openssh &&
ufw allow in 80/tcp &&
ufw allow in 443/tcp &&
ufw allow in 4443/tcp &&
ufw allow in 5222/tcp &&
ufw allow in 5347/tcp &&
ufw allow in 10000:20000/udp

Open /etc/prosody/prosody.cfg.lua and

Add above lines after admins object

admins = {}

component_ports = { 5347 }
component_interface = "0.0.0.0"
change

c2s_require_encryption=true
to

c2s_require_encryption=false
and check if on end of file has

Include "conf.d/*.cfg.lua"


Prosody manual plugin configuration
Setup issuers and audiences
Open /etc/prosody/conf.avail/<host>.cfg.lua

and add above lines with your issuers and audiences

asap_accepted_issuers = { "jitsi", "smash" }
asap_accepted_audiences = { "jitsi", "smash" }
Under you domain config change authentication to "token" and provide application ID, secret and optionally token lifetime:
VirtualHost "jitmeet.example.com"
    authentication = "token";
    app_id = "example_app_id";             -- application identifier
    app_secret = "example_app_secret";     -- application secret known only to your token
To access the data in lib-jitsi-meet you have to enable the prosody module mod_presence_identity in your config.
VirtualHost "jitmeet.example.com"
    modules_enabled = { "presence_identity" }
Enable room name token verification plugin in your MUC component config section:
Component "conference.jitmeet.example.com" "muc"
    modules_enabled = { "token_verification" }
Setup guest domain
VirtualHost "guest.jitmeet.example.com"
    authentication = "token";
    app_id = "example_app_id";
    app_secret = "example_app_secret";
    c2s_require_encryption = true;
    allow_empty_token = true;
Enable guest domain in config.js
Open your meet config in /etc/jitsi/meet/<host>-config.js and enable

var config = {
    hosts: {
        ...
        // When using authentication, domain for guest users.
        anonymousdomain: 'guest.jitmeet.example.com',
        ...
    },
    ...
    enableUserRolesBasedOnToken: true,
    ...
}
Edit jicofo sip-communicator in /etc/jitsi/jicofo/sip-communicator.properties
org.jitsi.jicofo.auth.URL=XMPP:jitmeet.example.com
org.jitsi.jicofo.auth.DISABLE_AUTOLOGIN=true
Edit jicofo config in /etc/jitsi/jicofo/config
SET the follow configs

JICOFO_HOST=jitmeet.example.com
And edit videobridge config in /etc/jitsi/videobridge/config
Replace

JVB_HOST=
TO

JVB_HOST=jitmeet.example.com
And add after JAVA_SYS_PROPS

JAVA_SYS_PROPS=...
AUTHBIND=yes
Then, restart all services

systemctl restart nginx prosody jicofo jitsi-videobridge2

Jitsi confirguration files
Authentication and virtual host defined here
nano /etc/prosody/conf.avail/meet.cgl360.ca.cfg.lua
nano /etc/jitsi/meet/meet.cgl360.ca-config.js

Header
{
  "alg": "HS256",
  "typ": "JWT"
}
JWT Payload
{
  "context": {
    "user": {
      "avatar": "https:/gravatar.com/avatar/abc123",
      "name": "John Doe",
      "email": "jdoe@example.com",
      "id": "abcd:a1b2c3-d4e5f6-0abc1-23de-abcdef01fedcba"
    }
  },
  "aud": "jitsi",
  "iss": "meetcgl360user",
  "sub": "meet.cgl360.ca",
  "room": "tnroom"
}

After change in above file restart

systemctl restart prosody
systemctl restart jicofo
systemctl restart jitsi-videobridge2


Token

apt-get install libssl-dev
apt-get install luarocks
apt-get install liblua5.2-0
apt-get install liblua5.2-dev
luarocks install luaossl
luarocks install jwt-jitsi
apt-get install jitsi-meet-tokens


Jibri machine startup

prosodyctl register recorder recorder2.meet.cgl360.ca RecordersPass

systemctl restart prosody
systemctl stop jicofo
systemctl stop jitsi-videobridge2
systemctl restart jibri
systemctl enable --now jibri


Life cycle Hook for rooms

jitsi-meet/prosody-plugins/mod_muc_meeting_id.lua
for room creation

roomdestroy for destroying the event


curl -X POST \
  http://18.217.162.193/dev/cgl360/jitsi/api/setJibriconferencestatus \
  -H 'Content-Type: application/json' \
  -H 'cache-control: no-cache' \
  -d '{"process":"on"}'

/etc/init.d/prosody reload
/etc/init.d/jicofo reload
systemctl restart prosody
systemctl restart jicofo
systemctl restart jitsi-videobridge2


Host file
3.137.116.33    recording.meet.cgl360.ca
3.137.116.33    recorder.meet.cgl360.ca
3.136.38.210    recording.meet.cgl360.ca
3.136.38.210    recorder.meet.cgl360.ca
3.18.96.178    recording.meet.cgl360.ca
3.18.96.178    recorder.meet.cgl360.ca
18.217.118.92    recording.meet.cgl360.ca
18.217.118.92    recorder.meet.cgl360.ca


Get Room details and reload setting
/etc/init.d/prosody reload
/etc/init.d/jicofo reload
systemctl restart prosody
systemctl restart jicofo
systemctl restart nginx

nano /usr/lib/prosody/modules/mod_muc_size.lua
nano /etc/nginx/sites-enabled/meet.cgl360.ca.conf
