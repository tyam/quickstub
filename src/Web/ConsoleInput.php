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

use Psr\Http\Message\ServerRequestInterface;

class ConsoleInput
{
    public function __invoke(ServerRequestInterface $request)
    {
        // collect args (path0, path1, ..., $form)
        $args = array_values($request->getAttributes());
        switch (strtoupper($request->getMethod())) {
            case 'HEAD': 
            case 'GET': 
            case 'DELETE': 
                $args[] = $request->getQueryParams();
                break;
            case 'POST': 
            case 'PATCH':
            case 'PUT': 
                $args[] = $request->getParsedBody();
                break;
        }
        return $args;
    }
}