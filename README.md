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
