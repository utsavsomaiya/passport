<p align="center">
    <a
        href="https://artisanssoultions.com"
        target="_blank"
    >
        <picture>
            <source
                width="300"
                media="(prefers-color-scheme: dark)"
                srcset="./public/images/logo/dark-mode-300.png"
            >
            <img
                alt="Artisans logo"
                src="./public/images/logo/light-mode-300.png"
            >
        </picture>
    </a>
</p>

## Product Xperience Manager

### Requirements
- PHP 8.2
- Other [Laravel requirements](https://laravel.com/docs/10.x/deployment#server-requirements)

### Installation
- Clone the repo: git clone [REPO_URL] [DIRECTORY_NAME]
- Create `.env` file from the example file: `php -r "file_exists('.env') || copy('.env.example', '.env');"`
- Install the dependencies: `composer install`
- Generate Key: `php artisan key:generate`
- DB migrate: `php artisan migrate`
- Public images: `php artisan storage:link`
- Please setup the [scheduler](#scheduled-tasks) (cronjob) on the server.
- We are using the [redis](#redis) for caching and queued jobs. Please install accordingly. Media library also uses the redis queue connection.
- Setup [Horizon](#horizon)
- [Seed data]('#data-seeding') as per your environment


### Make commands
We have created some ‘make’ commands. Feel free to use these commands:
    - make:enum
    - make:query

### Scheduled tasks
- Remove expired Sanctum tokens after 24 hours.
- [Horizon SnapShot](https://laravel.com/docs/10.x/horizon#metrics)
- Remove auditable entries after 180 days by default. This can be changed by setting the `DELETE_AUDITABLE_RECORDS_OLDER_THAN_DAYS` .env variable.

### Postman
To generate Postman API Keys, please follow these steps:
1. Visit the Postman documentation: [Postman API Key Authentication](https://learning.postman.com/docs/developer/postman-api/authentication/).
1. Follow the instructions provided in the documentation to generate your API keys.

### Data Seeding

If you are deploying to a production website, please run `php artisan db:seed --class=StaticSeeder`
It will create:
    - A super admin account

If you are setting up this project locally, please run `php artisan db:seed`

#### Note about Postman
This project offers an extra convenience to developers with Postman.
As per our documentation in Postman, setting the {{token}} variable 'undefined' runs the pre-request script.
Running the seeder when the `POSTMAN_API_KEY` .env variable is set resets the token by making an API call.

### Upgrade
- When updating [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum), it is important to consider any changes related to the Sanctum token. If the token implementation or behavior has been modified in the update, corresponding changes should be made in your code.

- Methods Using Sanctum Tokens In our application, there is a method that utilize Sanctum tokens:
1. [`PersonalAccessToken::findToken()`](./app/Http/Middleware/AddCompanyIdInServiceContainer.php#L24C53-L24C62)


### Roles and Permissions Caching
- We are caching the roles and permissions of all the users for better performance. The caching happens the first time user make any request that required authorization.

- Cache key is something like - `roles_and_permissions_of_user_[USER_ID]`

- The cache busting happens when any changes are made to the users, roles, or permissions. If you make any such changes directly (via tinker or DB), please remove the cache entry of the respective user or fire the queued job to remove cache entries for all users.

### Redis
- Install [redis](https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-redis-on-ubuntu-22-04) and [phpredis](https://github.com/phpredis/phpredis):
    ```shell
      sudo apt install -y redis-server
      sudo nano /etc/redis/redis.conf
   ```
    - Configure redis as follows:

    ```editorconfig
    . . .

    # If you run Redis from upstart or systemd, Redis can interact with your
    # supervision tree. Options:
    #   supervised no      - no supervision interaction
    #   supervised upstart - signal upstart by putting Redis into SIGSTOP mode
    #   supervised systemd - signal systemd by writing READY=1 to $NOTIFY_SOCKET
    #   supervised auto    - detect upstart or systemd method based on
    #                        UPSTART_JOB or NOTIFY_SOCKET environment variables
    # Note: these supervision methods only signal "process is ready."
    #       They do not enable continuous liveness pings back to your supervisor.
    supervised systemd

    . . .
    ```
    - Restart `redis`
    ```shell
    sudo systemctl restart redis.service
    ```

    - Test `redis`
    ```shell
    redis-cli
    ```
    - In the prompt that follows, test connectivity with the `ping` command and you should get `pong` response.

- Install [pickle](https://github.com/FriendsOfPHP/pickle):
```shell
wget https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar
```
- Now install `phpredis` and use all the default settings.
```shell
sudo php pickle.phar install redis
```
- Setup Horizon, and set .env variables accordingly.

### [Horizon](https://laravel.com/docs/10.x/horizon)
- If multiple sites are using Horizon on the same server, prefixes need to be added.
- Accessing the Horizon dashboard can be achieved by navigating to the following URL:
    - Horizon Dashboard: `APP_URL/horizon/dashboard`
- If you encounter a `403|Forbidden` error in a production environment while attempting to access the dashboard, kindly reach out to the developer team for assistance.

### [Development Principles](./Principles.md)
