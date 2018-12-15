<?php namespace YiZan\Exceptions;

use Exception, Config,View;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		if (SERVICE_DOMAIN == 'api'){
			$info = array(
				'code' 	=> 99999,
				'data' 	=> null,
				'msg'	=> '程序处理错误',
				'debug' => $e->getMessage(),
			);
			die(json_encode($info));
		}
        if (SERVICE_DOMAIN != 'api'){
             if(!empty($e)){
                 $c = CONTROLLER_NAME != "CONTROLLER_NAME" ? CONTROLLER_NAME : 'Index';
                 $a = ACTION_NAME != "ACTION_NAME" ? ACTION_NAME : 'index';
                 View::share('c',$c);
                 View::share('a',$a );
                 echo View::make("error._layouts.error");
                 die;
                 //统一跳转404
             }
        }
		return parent::render($request, $e);
	}
}