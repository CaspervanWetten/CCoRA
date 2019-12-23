# Coverability OZP
This is the repository for the Information Science research project by
Lucas Steehouwer (4172248, l.steehouwer@students.uu.nl).

# Installation
To install the server you need to have
[composer](https://www.getcomposer.org "get composer") installed. Once
installed, you must run `composer install` to install the required
dependencies. Then, you must set up the project's namespaces by
running `composer dumpautoload -o`. In the `deploy/` folder an
sql-file is included containing commands to set up the database.

## Configuration 
All of the configuration takes place in the files in the `config/`
folder. The only file that you **need** to set up is the
`database.php` file. In this file you have to specify the database's
dsn, user and password.

# Api
The application works with JSON only. There is no support for xml or
other formats.
## Users
There are 3 routes regarding the management of users. These are as
follows:

1. GET: `users/` gets all registered users
2. GET: `users/{id}` gets a specific user
3. POST: `users/new` sets a new user. Requires a JSON body in the
   request with a `name` key.

### Getting Users
Regardless of whether you're getting a single user, or a list of them,
each user resource comes with 3 keys: `id`, `name`, and
`created_timestamp`.

### Setting Users
To set a user you need to send a JSON object to the provided url. This
JSON object requires only one key: `name`. The server responds with
JSON containing the `id`for the user, and a URL belonging to the User
resource.

## Petri nets
1. GET `petrinet/{id}` get a specific Petri net
2. GET `petrinet/{limit}/{page}` get a page of Petri nets
3. GET `petrinet/{id}/image` get an SVG image of a specific Petri net
4. POST `petrinet/{uid}/new` upload a new Petri net. The Petri net is
   associated with the user with `user_id=uid`
5. POST `petrinet/{uid}/{pid}/{sid}/feedback` Receive feedback for a
   coverability graph. This coverability graph is about the Petri net
   with `id=pid`. The coverability graph is logged with
   `session_id=sid`. Send the coverability graph in JSON format.

## Sessions
1. GET `session/{uid}` get the current session for the user with
   `id=uid`
2. POST `session/{uid}/new_session` start a new session for the user
   with `id=uid`
