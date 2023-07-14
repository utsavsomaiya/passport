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

## Product Experience Management

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
2. `HasApiTokens` trait has `createToken`. we overwrite [it](./app/Models/User.php#L70-L87).

### [Horizon](https://laravel.com/docs/10.x/horizon) Access
- You can access the Horizon dashboard by visiting the following URL:
- Horizon Dashboard : `APP_URL/horizon/dashboard`
- Please note that only the Super Admin role has the necessary privileges to access this dashboard.
- If you have not the Super Admin role, you will not be able to log in or access the Horizon features.
