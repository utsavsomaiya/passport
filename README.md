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

## Product Xperience Management

### Requirements
- PHP 8.2
- Other [Laravel requirements](https://laravel.com/docs/10.x/deployment#server-requirements)

### Installation
- Clone the repo: git clone [REPO_URL] [DIRECTORY_NAME]
- Create `.env` file from the example file: `php -r "file_exists('.env') || copy('.env.example', '.env');"`
- Install the dependencies: `composer install`
- Generate Key: `php artisan key:generate`
- DB migrate: `php artisan migrate`
- Please setup the [scheduler](#scheduled-tasks) (cronjob) on the server.
- Setup [Horizon](#horizon)


### Make commands
We have created some ‘make’ commands. Feel free to use these commands:
    - make:enum
    - make:query

### Scheduled tasks
- Remove expired Sanctum tokens after 24 hours.
- [Horizon SnapShot](https://laravel.com/docs/10.x/horizon#metrics)


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

### Upgrade
- When updating [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum), it is important to consider any changes related to the Sanctum token. If the token implementation or behavior has been modified in the update, corresponding changes should be made in your code.

- Methods Using Sanctum Tokens In our application, there are two methods that utilize Sanctum tokens:
1. [`PersonalAccessToken::findToken()`](./app/Http/Middleware/AddCompanyIdInServiceContainer.php#L24C53-L24C62)

### [Horizon](https://laravel.com/docs/10.x/horizon)
- You can access the Horizon dashboard by visiting the following URL:
- Horizon Dashboard : `APP_URL/horizon/dashboard`
- Please note that only the Super Admin role has the necessary privileges to access this dashboard.

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

### [Development Principles](./Principles.md)
