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
* (server) Refactoring of the Petri net model. The current model is inefficient and incredibly taxing on the file system.
* (client) The ability to scale and pan the view of the modeller.
