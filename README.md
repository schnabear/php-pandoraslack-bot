# PHP Slack Bot Pandorabots Command

A Pandorabots implementation on Slack using PHP Slack Bot

## Installation

Create a new composer.json file and add the following...
```
{
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/schnabear/php-pandoraslack-bot.git"
        }
    ],
    "require": {
        "schnabear/php-pandoraslack-bot": "dev-master"
    }
}
```

Then run...
```
composer install
```

## Usage

```php
require 'vendor/autoload.php';

define('PB_BOTID', 'BotID');
define('SLACK_TOKEN', 'SlackToken');

$pandora = new \PhpPandoraSlackBot\PandoraCommand(PB_BOTID);
$bot = new \PhpSlackBot\Bot();
$bot->setToken(SLACK_TOKEN);
$bot->loadCatchAllCommand($pandora);
$bot->run();
```
