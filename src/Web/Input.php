<?php
/**
 * ConsoleInput
 * 
 * コンソール固有のInput。
 * ルートにパラメータがあれば、それを出現順にリストアップし、最後にフォームを付加する。
 * フォームは、メソッドがHEAD、GET、DELETEの場合はクエリ文字列を、
 * メソッドがPOST、PATCH、PUTの場合はparsedBodyとする。
 * たとえば、GET /comment/3/12?format=jsonの場合であれば、抽出される入力は
 * `[3, 12, ['format' => 'json']]`
 * となる。
 */

namespace Web;

use Psr\Http\Message\ServerRequestInterface as Request;
use tyam\fadoc\Converter;
use tyam\radarx\PayloadFactory;

class Input
{
    private $converter;

    public function __construct(Converter $converter) 
    {
        $this->converter = $converter;
    }

    /*public function __invoke(Request $request)
    {
        $args = $this->collectParameters($request);
        $form = $this->collectForm($request);
        $args[] = $form;
        $args[] = new PayloadFactory($form);
        return $args;
    }*/

    public function __invoke(Request $request)
    {
        $args = $this->collectArgs($request);
        $cnt = count($args);
        $form = $this->collectForm($request);
        $args[$cnt - 2] = $form;
        $args[$cnt - 1] = new PayloadFactory($form);
        return $args;
    }

    protected function collectform(Request $request)
    {
        switch (strtoupper($request->getMethod())) {
            case 'HEAD': 
            case 'GET': 
                return $request->getQueryParams();
            case 'POST': 
            case 'PATCH':
            case 'PUT': 
                return $request->getParsedBody();
            default: 
                return [];
        }
    }

    protected function resolveDomainMethod(Request $request)
    {
        $domain = $request->getAttribute('radar/adr:route')->domain;
        if (! $domain) {
            // domain not specified
            return null;
        }
        if (is_array($domain)) {
            $cname = $domain[0];
            $mname = $domain[1];
        } else {
            $cname = $domain;
            $mname = '__invoke';
        }
        return [$cname, $mname];
    }

    protected function collectArgs(Request $request)
    {
        list($cname, $mname) = $this->resolveDomainMethod($request);
        $cref = new \ReflectionClass($cname);
        $mref = $cref->getMethod($mname);

        $ps = $mref->getParameters();
        $nps = count($ps);
        $params = [];

        // リンク層のメソッドのパラメータは($param1, $param2, ..., $form, $payloadFactory)。
        // ここでは$paramNを集めるので後ろの2つは除外する。
        for ($i = 0; $i < $nps - 2; $i++) {
            $p = $ps[$i];
            $name = $p->getName();
            $val = $request->getAttribute($name);
            $params[$i] = $val;
        }

        // 残り2つの引数をダミーで埋める。
        $params[$nps - 2] = [];  // array
        $params[$nps - 1] = [[]];  // new PayloadFactory(array)

        $cd = $this->converter->objectize([$cname, $mname], $params, Converter::REPAIR);
        if (! $cd()) {
            throw new \Exception('aha: '.$cd->describe());
        }
        return $cd->get();
    }
}