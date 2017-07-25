<?php
namespace app\manage\controller;
use \think\Controller;
use \think\Url;
use \think\Session;
use \think\Validate;
use \app\manage\model\User;

class Sign extends Controller{
    
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     *登录
     */
    public function signin(){
        if (!$this->request->isAjax()) {
            if (Session::has('manage', 'management')) {
                $this->redirect(Url::build('/manage/index', null, true, DOMAIN));
            }
            return $this->fetch('sign/signin', ['data' => 0]);
        } else {
            if (!$this->request->has('username', 'post')) {
                $this->error('登陆账号必须');
            }
            if (!$this->request->has('passwd', 'post')) {
                $this->error('登陆密码必须');
            }
            if (!$this->request->has('captcha', 'post')) {
                $this->error('验证码必须');
            }
            $loginData = [
                'user_name' => $this->request->post('username/s'),
                'user_password' => $this->request->post('passwd/s'),
                'captcha' => $this->request->post('captcha/s'),
                '__token__' => $this->request->post('__token__/s'),
            ];
            
            $validate = new Validate(
                [
                    'user_name'=>'require|max:25|token',
                    'user_password'=>'require|max:20|min:6',
                    'captcha|验证码'=>'require|captcha'
                ],
                [
                    'user_name.require'=>'用户名必须',
                    'user_name.max'=>'用户名或密码错误',
                    'user_password.require'=>'用户名或密码错误',
                    'user_password.max'=>'用户名或密码错误',
                    'user_password.min'=>'用户名或密码错误',
                ]
            );
            if (!$validate->check($loginData)) {
                //重新生成令牌
                $this->error($validate->getError(), null, ['__token__'=>$this->request->token('__token__')]);
            }
            $userModel = new User();
            $userInfo = $userModel->signIn($loginData['user_name'], $loginData['user_password']);
            
            if (false === $userInfo) {
                $this->error('用户名或密码错误', null, ['__token__'=>$this->request->token('__token__')]);
            }
            if (is_int($userInfo)) {
                if ($userInfo == 0) {
                    $this->error('登录错误次数过多，已锁定，请24小时候再次登录', null, ['__token__'=>$this->request->token('__token__')]);
                } else {
                    $this->error("用户密码错误，还有{$userInfo}次登录机会", null, ['__token__'=>$this->request->token('__token__')]);
                }
            }
            Session::set('manage', ['admin'=>$userInfo], 'management');
            $this->success('登录成功', Url::build('/manage/index', null, true, DOMAIN));
        }
    }
    /**
     *登出
     */
    public function logout(){
        Session::clear('management');
        $this->success('已登出');
    }
}