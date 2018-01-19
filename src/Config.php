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
use tyam\radarx\ServiceLocator;

class Config extends ContainerConfig
{
    const APP_NAME = 'myapp';

    public function define(Container $di)
    {
        // 各種サービス
        // ---------------------------------------------------------

        // Logger
        $di->set('logger', $di->lazyNew('Monolog\Logger'));
        $di->params['Monolog\Logger'][0] = self::APP_NAME;

        // Session
        $di->set('session', $di->lazyNew('Infra\PhpSession'));

        // Dispatcher
        $di->set('dispatcher', $di->lazyNew('tyam\edicue\Dispatcher'));
        $di->params['Aura\Di\ResolutionHelper']['container'] = $di;
        $di->params['tyam\edicue\Dispatcher'][0] = $di->lazyNew('Aura\Di\ResolutionHelper');
        $di->params['tyam\edicue\Dispatcher'][1] = null;
        $di->params['tyam\edicue\Dispatcher'][2] = [
            // ここにリスナを登録していく。
            'Domain\AccessEvent' => ['App\AccessEntry']
        ];
        
        // レポジトリ（インターフェイス）とマッパ（実装）の関連付け
        // ---------------------------------------------------------
        $di->types['Domain\UserRepository'] = $di->lazyNew('Infra\UserMapper');
        $di->types['Domain\StubRepository'] = $di->lazyNew('Infra\StubMapper');
        $di->types['Domain\AccessRepository'] = $di->lazyNew('Infra\AccessMapper');

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

        // PDOのセットアップ
        $pdo = $di->get('db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        //phpinfo();exit;

        // サービスロケータの設定
        new class('Session', $di->get('session')) extends ServiceLocator {};
        new class('Logger', $di->get('logger')) extends ServiceLocator {};
        new class('Dispatcher', $di->get('dispatcher')) extends ServiceLocator {};
    }
}
