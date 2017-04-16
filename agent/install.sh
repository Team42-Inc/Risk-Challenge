#!/usr/bin/env bash
# Catch arguments
if [ "$#" -ne 2 ]; then
    echo "Illegal number of parameters. Required arguments: <SERVER> <KEY>"
    exit 1
fi
SERVER=$1
KEY=$2

# Install dependencies
apt-get update
apt-get install -y rkhunter chrootkit

# Collect data
WEBSERVER=(apache2 nginx tomcat)
for i in ${WEBSERVER[@]}; do
  testcase=`which $i`
  if [ -z "$testcase" ]; then
    echo "$i not installed"
  else
    echo "$i installed"
    WEBSITE=`ls /etc/$i/sites-enabled/`
    URL=()
    for j in ${WEBSITE[@]}; do
      URL+=($(grep '^ServerName' "/etc/$i/sites-available/$j" | sed "s/ServerName /%/" | cut -d'%' -f2))
    done
    echo "${#URL[@]} detected websites"
  fi
done
IP=$(hostname -I | cut -d' ' -f1)
echo "Current IP is $IP"
UUID=$(cat /proc/sys/kernel/random/uuid)
AGENTNAME="agent-$UUID"
echo "Agent's name is $AGENTNAME"
HASH=$(echo $UUID | sha256sum | cut -d' ' -f1)

# Push data
PAYLOAD='{"payload":{"r":"$HASH","a":"$AGENTNAME","s":"$KEY","ip":"$IP","url":"$URL"}}'
curl -i -H "Accept: application/json" -H "Content-Type:application/json" -X POST --data "$PAYLOAD" "$SERVER"

# Analysis
OS=`uname -a`
lsof -i > output.log
rkhunter -c -q --rwo
curl -i -X POST -F "type=rootkit" -F "host=$IP" -F "osInfo=$OS" -F "openPorts=@output.log" -F "rootkitWarning=@/var/log/rkhunter.log" http://10.0.2.57:8080/servers/data
