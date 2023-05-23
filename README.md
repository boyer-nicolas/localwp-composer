# [PRE-ALPHA] LocalWP w/ Composer - A Git compatible WordPress development environment

## DISCLAIMER

THIS PROJECT IS IN PRE-ALPHA STAGE, USE AT YOUR OWN RISK. DO NOT USE IN PRODUCTION (yet).

This project aims to combine the incredible power of [LocalWP](https://localwp.com/) with the flexibility of [Composer](https://getcomposer.org/).
This architecture is similar to [Bedrock](https://roots.io/bedrock/), but with a main difference, it's compatible with LocalWP out of the box and does not require any additional server configuration (development, staging, production, etc.).
Works on Linux, macOS and Windows (with Git Bash as full WSL2 support is not available on LocalWP for Windows yet).

## Requirements

- [LocalWP](https://localwp.com/)
- [Composer](https://getcomposer.org/)
- [Git](https://git-scm.com/)

## Installation

1. Create a new site in LocalWP & stop it when it autostarts
2. Open the site in your file browser
3. Delete the `app` directory
4. Open a terminal in the site directory (with LocalWP so it loads composer, wp-cli etc.)
5. Clone this repo into the site directory
6. Run `composer install` to install WordPress core
7. Run `composer gen:env` to generate the `.env` file (see `.env.example` for options)
8. Start the site in LocalWP

## Usage

### Media

The system does not require many commands to be run, but there is a downside, you need a distant server to use the `media` feature.
Why ? Well, pushing your uploads folder to Git can be either very long or very expensive, so I decided to use a distant server to store the media files.
The upside of using this is that it uses rsync instead of the git ssh protocol to transfer the files, which is much faster.
The downside is that there is no versioning of the media files, but I think it's a fair tradeoff as versioning media files is not very useful anyways.
When I talk about media files, I also mean the database exports (placed in the `sql` folder).
To prevent losing your media files, a `branching` system is implemented, which means that every time you create a new branch, the media files are copied to a new folder with the branch name (be careful, this can take a while if you have a lot of media files and can take a lot of space on your server).

To use this feature, you need to have a distant server with SSH access and rsync installed.
You also need to have a folder on the server where you want to store the media files (which is created automatically if it doesn't exist).
I recommend using a tool like [Filebrowser](https://filebrowser.org/) to manage the files on the server.

When prompted by the env generator script, enter the SSH connection params.
I recommend not using a password and using a SSH key instead, but it's up to you.
I also recommend using a non-root user, but it's also up to you.
And finally, do not use the default port (22), it's a security risk (but you already knew that, right ?).

### Commands

#### `composer env:generate` - Generate the `.env` file

#### `composer env:upload` - Upload the `.env` file variables to Git

#### `composer db:dump` - Dump the database to a file

#### `composer db:backup` - Dump the database to a file with the .bak extension

#### `composer db:import` - Import the database from a file

#### `composer media:pull` - Pull the media & sql files from the server

#### `composer media:push` - Push the media & sql files to the server

#### `composer hooks:set` - Set the git hooks pointing to the .githooks directory

#### `composer hooks:unset` - Unset the git hooks pointing to the .githooks directory

#### `composer clean` - Clean the project (remove the vendor directory, the composer.lock file and the .env file)

### Hooks

#### `pre-commit` - Export the database and push the media files to the server

#### `post-commit` - Run the test suite (if any)

## Deployment

This boilerplate comes with a custom designed [deployer](https://deployer.org/) config.
Configure it to your liking.

#### `dep deploy` - Deploy the site

#### `dep ssh` - SSH into the server

## Contributing

Pull requests are welcome.
For major changes, please open an issue first to discuss what you would like to change.

## License

[MIT](https://choosealicense.com/licenses/mit/)

## Credits

- [johnpbloch/wordpress](https://packagist.org/packages/johnpbloch/wordpress)
- [vlucas/phpdotenv](https://packagist.org/packages/vlucas/phpdotenv)
- [oscarotero/env](https://packagist.org/packages/oscarotero/env)
- [roots/wp-config](https://packagist.org/packages/roots/wp-config)
- [roots/wp-password-bcrypt](https://packagist.org/packages/roots/wp-password-bcrypt)
- [roots/bedrock-autoloader](https://packagist.org/packages/roots/bedrock-autoloader)

# LOCALWP-COMPOSER
