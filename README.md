# CoRA - Coverability Analyzer

CoRA is an intelligent tutoring system (ITS) aimed at teaching students the conversion of a Petri net to a coverability graph.

CoRA consists of two components, a client and a server. The server provides all the mechanisms to provide feedback to the user. The client is an implementation of a front-end surrounding the server.

## TODO
A list of required features and improvements, sorted by the MoSCoW method:
* (server) The ability to provide hints. In case the user gets stuck, the user needs to be able to request (part of) the answer so they won't get stuck for too long.
* (server) Feedback messages regarding the introduction and use of the \omega identifier for Petri net places.
* (server) Refactoring of the Petri net model. The current model is inefficient and incredibly taxing on the file system.
* (client) The ability to scale and pan the view of the modeller.
