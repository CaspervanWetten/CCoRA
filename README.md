# CoRA - Coverability Analyzer

CoRA is an intelligent tutoring system (ITS) aimed at teaching students the conversion of a Petri net to a coverability graph.

CoRA consists of two components, a client and a server. The server provides all the mechanisms to provide feedback to the user. The client is an implementation of a front-end surrounding the server.

## Installation
Installation of CoRA requires a little bit of setup.
### Server
The server part of CoRA contains a configuration file, `config.php`. Fill out the details of your environment in this file. You need two folders for storing logs and temporary user files. These two folders can be located anywhere on the server's file system, but you have to make these folders yourself. Make sure the server has `rwx` permissions for these folders.

### Client
The client only needs to know what the base URL is of the server. You need to provide this address in `Main.ts`. After doing so you need to compile the client-side application yourself. Make sure you compile to a single JavaScript file, like so: `tsc Main.ts --outFile main.js`.

## TODO
A list of required features and improvements, sorted by the MoSCoW method:
* (server) The ability to provide hints. In case the user gets stuck, the user needs to be able to request (part of) the answer so they won't get stuck for too long.
* (server) Feedback messages regarding the introduction and use of the \omega identifier for Petri net places.
* ~~(server) Refactoring of the Petri net model. The current model is inefficient and incredibly taxing on the file system.~~
* ~~(server) A refactoring of the logger. Particularly the Session controller's functionality should be extended, and there should be a better management of exceptions.~~
* ~~(server) A more sophisticated logger. The new logger should split the sessions over multiple files, instead of keeping all the logs regarding a specific user in a single file. This is less taxing on the server's file system.~~
* ~~(server) A better implementation of the Petri net data structure. The current data structure works fine, but the way flows are stored is inefficient and filtering flows based on certain properties is a headache. A proposed solution would be to store the flows in a way that is more analogous to the mathmatical description of Petri nets, i.e. `(P x T) U (T x P) -> N)`.~~
* (client) The ability to scale and pan the view of the modeller.
* ~~(server) Implement CoRA's own exception class. This class should have an http status code to be served with the exception itself. We then would have a uniform method of throwing errors. The current solution is calling the `showError` method of the `Controller` class, but this is horribly ugly and is often forgotten, resulting in a mishmash of exceptions and `showError` calls.~~
* ~~(server) Rename namespaces. CoRA was initially known under the name `Cozp`. This name persists as the base name for all the namespaces of the server. These need to be updated.~~
* (both) Add install script to automatically configure deployment
