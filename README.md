## API Management for Football Club "Barcelona" Players

### Models
- **Player** with attributes: name, position, age, nationality, number of goals for the season.
- **Team** model with information about the team and connection to players.

### API Endpoints
- `GET /players` - Returns a list of all players with available filters:

  - `?page`: Which page to look at.
  - `?per_page`: How many records per page.
  - `?position`: Player's position. Options: 'Goalkeeper', 'Defender', 'Midfielder', 'Forward'.
  - `?name`: Player's name.
  - `?team_id`: ID of player's team.
- `GET /players/{id}` - returns detailed information about a specific player.
- `POST /players` - creates a new player, example:
 ```json
{
  "team_id": 107,
  "name": "Player name",
  "position": "Forward",
  "age": 26,
  "nationality": "Portugal",
  "goals_season": 12
}
```
- `PUT /players/{id}` - updates information about a specific player, example:
 ```json
{
  "team_id": 107,
  "name": "Player name",
  "position": "Forward",
  "age": 26,
  "nationality": "Portugal",
  "goals_season": 12
}
```

- `DELETE /players/{id}` - deletes a player.
- ---


- `GET /teams` - returns a list of all teams.
- `GET /team/{id}` - returns detailed information about a specific team.
- `POST /teams` - creates a new team, example:
 ```json
{
    "name": "team name",
    "coach": "Coach name",
    "founded_in": 2009,
    "stadium": "stadium name"
}
```
- `PUT /teams/{id}` - updates information about a specific team.
 ```json{
    "id": 106,
    "name": "team name",
    "coach": "Coach name",
    "founded_in": 2009,
    "stadium": "stadium name"
}
```
- `DELETE /teams/{id}` - deletes a team.


## Seeders
- **Seeders for Player and Team** have been implemented to populate the database with initial data, simplifying the testing and development process. You could populate the database with:
 `php artisan db:seed --class=PlayerSeeder`
 `php artisan db:seed --class=TeamSeeder`


## Authentication
- The API utilizes **Laravel Sanctum** for handling authentication.
- Users are required to **register** or **log in** to interact with the API.
- Upon successful registration or login, a **token is issued** to the user.

## Registration and Login:
### Registration Endpoint
- **Endpoint**: `POST /register`
- **Required fields**: `name`, `email`, `password`, `password_confirmation`.
- **Returns**: A token upon successful registration.

### Login Endpoint
- **Endpoint**: `POST /login`
- **Required fields**: `email` and `password`.
- **Returns**: A token upon successful login.

## Token Usage
- The **issued token** must be included in the header of each API request for authentication.
- **Header format**: `Authorization: Bearer <token>`

