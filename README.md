# CoRA - Coverability Reachability Analyzer
CoRA is een intelligent lesgeef systeem, gemikt op het helpen bij het maken van Coverability en Reachability graven voorPetri netten.

[CoRA is findable at here](http://cora.architecturemining.org/)

CoRA heeft een aantal praktische features wat het ideaal maakt voor het maken van Reach- en Coverability graven:
- .lola bestand support
- .pnml bestand support
- lightweight
- 'veel feedback' en 'weinig feedback' opties
- A small set of test nets in /petrinets/, practical for testing purposes

CoRA, initieel ontwikkeld door [Lucas Steehouwer](https://github.com/ArchitectureMining/CoRA) bestaat uit twee delen, de front-end [cmod](https://github.com/lsteehouwer/cmod) en de back-end (CoRA zelf), deze twee communiceren via een rest API


## Installatie
Gezien CoRA uit twee delen bestaat is er een andere installatie voor beide

### CoRA
CoRA zelf maakt gebruik van [composer](https://getcomposer.org/download/) voor dependency management. De laatste versie op het moment van schrijven is v2.5.5. Download en installeer volgens de website. Dan, run de volgende commandos in /CoRA2/server/
```
composer installa
composer dump-autoload -a
```
Er kan gebruikt gemaakt worden van de docker-compose file voor test doeleindes met
``` docker-compose up```, maar dit is **niet** voor praktijk gebruik.
Edit de docker-compose naar wens, standaard opent hij port 7000:80 voor de API, 9001:8080 voor adminer, en 40000:3306 voor de database.

### cmod
cmod (bovenal getypt in typescript) maakt gebruik van node package manager voor de dependency management. Gezien haar leeftijd vereist cmod specifiek nvm versie 10.24.1. Voor installatie, run de volgende commandos in /cmod/
```
nvm install 10.24.1
nvm use 10
nvm install
```
From then on, one can use the ```npm run start``` for a temporary build for testing purposes, or ```npm run build```, which'll build the files in the /cmod/distr/ folder

Nvm heeft, zeker op windows, soms moeite met de goede versie gebruiken, dit kan bekeken worden met ```nvm current```
Als de gebruikte port van de API wordt aangepast in CoRA's docker-compose, pas die dan ook aan in de config.json!


## REST API
cmod maakt gebruik van een REST API om met CoRA te praten, je kan postman en de ```cora_postman_collection.json``` gebruiken om dit te testen.
Er zijn een aantal dingen die gecommuniceerd worden:

### Users
1. GET: `user/{id}`: gets a specific user
2. GET: `user/[{limit}/{page}]`: list registered users
3. POST: `user/new`: register a new user. The request format can be
   FormData or JSON, as long as it has a `name` field containing the
   user name for the user. Do note that the user `name` should contain alphanumeric characters only
### Petri nets
1. GET `petrinet/{id}`: get a specific Petri net
2. GET `petrinet/[{limit}/{page}]`: list Petri nets
3. GET `petrinet/{id}/image`: get an SVG image of a specific Petri net
4. POST `petrinet/{uid}/new`: upload a new Petri net. The Petri net is
   associated with the user with `user_id=uid`. Upload a file with key
   `petrinet`. Use FormData for this.
5. POST `petrinet/feedback` Receive feedback for a coverability
   graph. Only JSON formats are accepted.
### Sessions
1. GET `session/current`: get the current session for the user. Supply
   the user id by putting it in the body of the request. The
   multipart/form-data media type does not work unfortunately. This is
   because of a limitation in both PHP and Slim.
2. POST `session/new`: create a new session for the user. A session
   describes how a user creates a coverability graph for a certain
   petrinet. As such, you need to supply the `user_id` and
   `petrinet_id` in the body of the request.

### Feedback
CoRA returns the feedback values in .json format, this is just one example:
```json
{
	"user_id": 1570,
	"petrinet_id": 1560,
	"session_id": 5,
	"initial_marking_id": 1557,
	"graph": {
		"states": [
			{
				"state": "p1: 2",
				"id": 0
			},
			{
				"state": "p1: 1, p2: ω",
				"id": 1
			}
		],
		"edges": [
			{
				"fromId": 0,
				"toId": 10,
				"transition": "t1",
				"id": 2
			}
		],
		"initial": {
			"id": 0
		}
	}
}
```
The `"id"` value refers to the internal id of a state (so the first added state has id=0). The `"state"` is a string describing a marking, e.g. `"p1: 2"` describes p1 having 2 tokens. Any non-numeric y value for `px: y` is seen as an omega (unbounded).

## TODO
A todo list, as ordered and indicated by the MoSCoW system
- **(m) Give an error if connected with a non-chromium browser!**
- (m) Don't force " tx : " labelling
- (m) better error handling
- - 'name already exists'
- - 'file error'
- - 'name error' 
- - 'same transition/place ids'
- (m) better scaling?
- - less margin inside nodes
- - less overlap labels/edges
- - disable the 'mirorring' that happens going from eclipse -> CoRA
- (m) improved label handling, dont use colons if there is no label
- (s) Don't render self-loop at the same place of another self-loop
- (s) Auto generate the reachability graph 
- (s) Clean non-alphanumeric characters from transition/place names
- (s) Clean non-alphanumeric characters from person names names
- (s) autogenerate transition/place ids if none are found/the same one is found multiple times
- (s) Graphviz file format support
- (c) 'Upload new file' inside the modeller


## Acknowledgements
CMOD is built upon the [Vue framework](https://vuejs.org) and uses [Vuex](https://vuex.vuejs.org) for state management. In addition, CMOD makes use of [vue-class-component](https://github.com/vuejs/vue-class-component), [vue-property-decorator](https://github.com/kaorun343/vue-property-decorator), and [vuex-module-decorators](https://github.com/championswimmer/vuex-module-decorators). For the modeller CMOD uses [vue-multipane](https://github.com/yansern/vue-multipane) for the layout. [Vue-focus](https://github.com/simplesmiler/vue-focus) is used for setting focus.
