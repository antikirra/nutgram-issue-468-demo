<?php

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

require_once __DIR__ . '/vendor/autoload.php';

$container = (new ContainerBuilder())->addDefinitions([
    LoggerInterface::class => function () {
        return (new Logger('logger'))->pushHandler(new StreamHandler(STDERR, Level::Debug));
    },
    Nutgram::class => function (ContainerInterface $container) {
        return new Nutgram((string)getenv('TG_TOKEN'), [
            'logger' => $container->get(LoggerInterface::class),
            'container' => $container // <-- COMMENT OUT THIS LINE TO MAKE THE CODE FUNCTIONAL !!!
        ]);
    }
])->build();

/**
 * @var Nutgram $bot
 */
$bot = $container->get(Nutgram::class);

$bot->onCommand('start', function (Nutgram $bot) {
    $keyBoard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make('Sign in to example.com', url: 'https://example.com/auth/confirm?token='))
        ->addRow(InlineKeyboardButton::make('Cancel', callback_data: 'cancel'));

    $bot->sendMessage('Press the button below to sign in on example.com', [
        'reply_markup' => $keyBoard
    ]);
});

$bot->onCallbackQueryData('cancel', function (Nutgram $bot) {
    $bot->deleteMessage($bot->chatId(), $bot->message()->message_id);
    $bot->answerCallbackQuery();
});

$bot->onMessage(function (Nutgram $bot) {
    $bot->sendMessage($bot->message()->getType());
});

$bot->onContact(function (Nutgram $bot) {
    $bot->sendMessage('hello');
});

$bot->onException(function (Nutgram $bot, \Throwable $exception) {
    $bot->sendMessage("Whoops! Something wrong...\nPlease, try again later!");
});

for (; ;) {
    $bot->run();
}
