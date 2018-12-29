# Coverability OZP
This is the repository for the Information Science research project by Lucas Steehouwer (4172248, l.steehouwer@students.uu.nl).

# Installation
Install the [Slim Framework](https://www.slimframework.com/) in the folder where
you want to host from. Then, git clone this repository.
Make sure the server user has permission to write in this directory.

## Configuration
CoRA requires some configuration  to work well with your server. Follow the instructions in `config.php`. You need to configure the database connection, where you want to store user data, and some Slim internal settings. In case you use a database that uses different table names than the standard database implementation, then you can also specify those name in the configuration file.

# Api
The application works with JSON only. There is no support for xml or other formats.
## Users
There are 3 routes regarding the management of users. These are as follows:

1. GET: `users/` gets all registered users
2. GET: `users/{id}` gets a specific user
3. POST: `users/new` sets a new user. Requires a JSON body in the request with a `name` key.

### Getting Users
Regardless of whether you're getting a single user, or a list of them, each user resource comes with 3 keys: `id`, `name`, and `created_timestamp`.

### Setting Users
To set a user you need to send a JSON object to the provided url. This JSON object requires only one key: `name`. The server responds with JSON containing the `id`for the user, and a URL belonging to the User resource.

## Petri nets
1. GET `petrinet/{id}` get a specific Petri net
2. GET `petrinet/{limit}/{page}` get a page of Petri nets
3. GET `petrinet/{id}/image` get an SVG image of a specific Petri net
4. POST `petrinet/{uid}/new` upload a new Petri net. The Petri net is associated with the user with `user_id=uid`
5. POST `petrinet/{uid}/{pid}/{sid}/feedback` Receive feedback for a coverability graph. This coverability graph is about the Petri net with `id=pid`. The coverability graph is logged with `session_id=sid`. Send the coverability graph in JSON format.

## Sessions
1. GET `session/{uid}` get the current session for the user with `id=uid`
2. POST `session/{uid}/new_session` start a new session for the user with `id=uid`
