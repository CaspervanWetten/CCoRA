# Coverability OZP
This is the repository for the Information Science research project by Lucas Steehouwer (4172248, l.steehouwer@students.uu.nl).

# Installation
Install the [Slim Framework](https://www.slimframework.com/) in the folder where
you want to host from. Then, git clone this repository.
Make sure the server user has permission to write in this directory.

## Configuration
In order to work properly you need to configure Cozp to work well with your server. Follow the instructions in `config.php`. You need to configure the database connection, where you want to store user data, and some Slim internal settings.

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

## Graphs
1. POST: `graphs/{id}/new` uploads a new .lola file and puts it in the correct folder.

# Dependencies
