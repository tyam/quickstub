<?php
/**
 * Config
 *
 * 当ソフト全体のDIコンフィグ。
 * インフラ的な部分のセットアップや、DIのセットアップなど。
 */

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\StreamHandler;

class Config extends ContainerConfig
{
    const APP_NAME = 'myapp';

    public function define(Container $di)
    {
        // アプリケーションのインフラ部分
        // ---------------------------------------------------------
        // App
        $di->params['Domain\App'][0] = $di->lazyGet('logger');
        $di->params['Domain\App'][1] = $di->lazyNew('tyam\edicue\Dispatcher');
        $di->params['Domain\App'][2] = $di->lazyGet('session');

        // Logger
        $di->set('logger', $di->lazyNew('Monolog\Logger'));
        $di->params['Monolog\Logger'][0] = self::APP_NAME;

        // Session
        $di->set('session', $di->lazyNew('Store\PhpSession'));

        // Dispatcher
        $di->params['Aura\Di\ResolutionHelper']['container'] = $di;
        $di->params['tyam\edicue\Dispatcher'][0] = $di->lazyNew('Aura\Di\ResolutionHelper');
        $di->params['tyam\edicue\Dispatcher'][1] = null;
        $di->params['tyam\edicue\Dispatcher'][2] = [
            // ここにリスナを登録していく。
        ];
        
        // レポジトリ（インターフェイス）とマッパ（実装）の関連付け
        // ---------------------------------------------------------
        $di->types['Domain\UserRepository'] = $di->lazyNew('Store\UserMapper');
        $di->types['Domain\StubRepository'] = $di->lazyNew('Store\StubMapper');
        $di->types['Domain\AccessRepository'] = $di->lazyNew('Store\AccessMapper');

        // データベースの設定
        // ---------------------------------------------------------
        $di->set('db', $di->lazyNew('PDO'));
        $di->params['PDO']['dsn'] = sprintf('mysql:dbname=%s;host=%s', getenv('DATABASE_NAME'), getenv('DATABASE_HOST'));
        $di->params['PDO']['username'] = getenv('DATABASE_USER');
        $di->params['PDO']['passwd'] = getenv('DATABASE_PASSWORD');
        $di->params['PDO']['options'] = array();
        $di->types['PDO'] = $di->lazyGet('db');
    }

    public function modify(Container $di)
    {
        // monologのセットアップ
        $logger = $di->get('logger');
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

        // Appのシングルトンインスタンスの生成
        $di->newInstance('Domain\App');
        class_alias('Domain\App', 'App');
    }
}
