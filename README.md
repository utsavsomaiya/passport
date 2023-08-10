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


### Make commands
We have created some ‘make’ commands. Feel free to use these commands:
    - make:enum
    - make:query

### Scheduled tasks
- Remove expired Sanctum tokens after 24 hours.
- [Horizon SnapShot](https://laravel.com/docs/10.x/horizon#metrics)
- Remove auditable entries after 180 days by default. This can be changed by setting the `DELETE_AUDITABLE_RECORDS_OLDER_THAN_DAYS` .env variable.


### [Prevent main branch direct pushes](https://hiltonmeyer.com/articles/protect-git-branch-and-prevent-master-push.html)
- Open terminal (not inside VS Code) and cd into the project directory

- touch .git/hooks/pre-push (to create the hook file)

- nano .git/hooks/pre-push (to edit the hook file)

- Paste the following content in it and save:

```shell
#!/bin/bash

protected_branch='main'
current_branch=$(git symbolic-ref HEAD | sed -e 's,.*/\(.*\),\1,')

if [ $protected_branch = $current_branch ]
then
    echo "${protected_branch} is a protected branch, create PR to merge"
    exit 1 # push will not execute
else
    exit 0 # push will execute
fi
```
- `chmod +x .git/hooks/pre-push` (to make the hook file executable)

### Postman
To generate Postman API Keys, please follow these steps:
1. Visit the Postman documentation: [Postman API Key Authentication](https://learning.postman.com/docs/developer/postman-api/authentication/).
1. Follow the instructions provided in the documentation to generate your API keys.

### Important Note for Data Seeding
If your application environment is **production**:
1. You need to create one Super Admin. Don't worry just fire one command: `php artisan db:seed --class=StaticSeeder`.

If you intend to remove the token while seeding the data, please ensure the following:
1. Set the environment variable of [Postman](#postman) accordingly.
1. Open your terminal and execute the following command: `php artisan db:seed`.

### Upgrade
- When updating [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum), it is important to consider any changes related to the Sanctum token. If the token implementation or behavior has been modified in the update, corresponding changes should be made in your code.

- Methods Using Sanctum Tokens In our application, there are two methods that utilize Sanctum tokens:
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
- You can access the Horizon dashboard by visiting the following URL:
- Horizon Dashboard : `APP_URL/horizon/dashboard`
- Please note that only the Super Admin role has the necessary privileges to access this dashboard.

### [Development Principles](./Principles.md)
