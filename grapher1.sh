#!/bin/bash
INPUT="/srv/collection/usercounter/anthony.freenode.net/in"
OUTPUT="/srv/collection/usercounter/anthony.freenode.net/out"
WRITE="/srv/collection/usergraph/data/"

## Begin main part of loopish thingy 
# Now we do the list stuff
echo "/LIST #botters" > $INPUT
sleep 10
echo "/LIST #transcendence" > $INPUT
echo "/LIST ##club-ubuntu" > $INPUT
## Begin Processing of User Data.
BOTTERS=`cat $OUTPUT | grep botters | tail -n1 | sed -e "s/\#botters //g" -e "s/ Botters \-.*//g"`
TRANSCENDENCE=`cat $OUTPUT | grep transcendence | tail -n1 | sed -e "s/\#transcendence //g" -e "s/ Latest.*//g" -e "s/ Welcome.*//g"`
CLUB=`cat $OUTPUT | grep club-ubuntu | tail -n1 | sed -e "s/\#\#club-ubuntu //g" -e "s/Welcome.*//g"`
BOTKITEERS=`cat $OUTPUT | grep botkiteers | tail -n1 | sed -e "s/\#\#botkiteers//g"`
echo $BOTTERS >> /srv/collection/usergraph/data/botters.txt
echo $TRANSCENDENCE >> /srv/collection/usergraph/data/transcendence.txt
echo $CLUB >> /srv/collection/usergraph/data/club-ubuntu.txt
echo $BOTKITEERS >> /srv/collection/usergraph/data/botkiteers.txt
