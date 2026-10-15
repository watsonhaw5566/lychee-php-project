<?php

declare(strict_types=1);

namespace app\controller;

use app\model\User;
use Lychee\auth\SatokenMiddleware;
use Lychee\http\JsonResponse;
use Lychee\http\Request;
use Lychee\routing\Middleware;
use Lychee\routing\Resource;
use Lychee\routing\ResourceController;
use Lychee\routing\Route;
use Lychee\routing\WithoutMiddleware;

/**
 * 后台用户：登录认证 + 用户资源 CRUD。
 */
#[Resource('/api/user')]
#[Middleware(SatokenMiddleware::class)]
class UserController extends ResourceController
{
    protected string $modelClass = User::class;

    protected array $uniqueFields = ['email'];

    /**
     * 邮箱登录。
     */
    #[Route('/login', 'POST')]
    #[WithoutMiddleware]
    public function login(string $email = '', string $password = ''): JsonResponse
    {
        $user = User::where('email', $email)->find();

        if (!$user || !password_verify($password, (string) $user->password)) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '邮箱或密码错误', 'data' => null], 400);
        }

        if ((int) $user->status !== 1) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '账号已被禁用', 'data' => null], 400);
        }

        $token = satoken()->login((int) $user->id, [
            'email'    => $user->email,
            'username' => $user->username,
            'role'     => $user->role,
        ]);

        $user->last_login_time = date('Y-m-d H:i:s');
        $user->save();

        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => ['token' => $token]]);
    }

    /**
     * 退出登录。
     */
    #[Route('/logout', 'POST')]
    public function logout(): JsonResponse
    {
        satoken()->logout();

        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => null]);
    }

    /**
     * 当前登录用户信息。
     */
    #[Route('/info', 'POST')]
    public function info(): JsonResponse
    {
        $user = User::find(satoken()->getCurrentLoginId());

        if (!$user) {
            return json(['errno' => 0, 'code' => 401, 'msg' => '用户不存在', 'data' => null], 401);
        }

        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => [
            'name'             => $user->username,
            'avatar'           => $user->avatar ?: '',
            'email'            => $user->email,
            'role'             => $user->role,
            'job'              => 'admin',
            'jobName'          => '系统管理员',
            'organization'     => 'Lychee',
            'organizationName' => 'Lychee',
            'location'         => '',
            'locationName'     => '',
            'introduction'     => '',
            'personalWebsite'  => '',
            'phone'            => '',
            'registrationDate' => $user->create_time,
            'lastLoginTime'    => $user->last_login_time ?: '',
            'accountId'        => (string) $user->id,
            'certification'    => 1,
        ]]);
    }

    /**
     * 修改当前登录用户的基础信息（用户名 / 邮箱）。
     */
    #[Route('/change_info', 'POST')]
    public function changeInfo(Request $request): JsonResponse
    {
        $user = User::find(satoken()->getCurrentLoginId());

        if (!$user) {
            return json(['errno' => 0, 'code' => 401, 'msg' => '用户不存在', 'data' => null], 401);
        }

        $data     = $request->post();
        $username = trim((string) ($data['username'] ?? $data['nickname'] ?? ''));
        $email    = trim((string) ($data['email'] ?? ''));

        if ($username === '' || $email === '') {
            return json(['errno' => 0, 'code' => 400, 'msg' => '用户名、邮箱不能为空', 'data' => null], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '邮箱格式不正确', 'data' => null], 400);
        }

        $exists = User::where('email', $email)->where('id', '<>', $user->id)->find();
        if ($exists) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '邮箱已存在，请更换', 'data' => null], 400);
        }

        $user->username = $username;
        $user->email    = $email;
        $user->save();

        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => null]);
    }

    /**
     * 修改当前登录用户的密码。
     */
    #[Route('/change_password', 'POST')]
    public function changePassword(Request $request): JsonResponse
    {
        $user = User::find(satoken()->getCurrentLoginId());

        if (!$user) {
            return json(['errno' => 0, 'code' => 401, 'msg' => '用户不存在', 'data' => null], 401);
        }

        $data            = $request->post();
        $password        = (string) ($data['password'] ?? '');
        $confirmPassword = (string) ($data['confirm_password'] ?? '');

        if (strlen($password) < 6) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '密码长度不能少于 6 位', 'data' => null], 400);
        }

        if ($password !== $confirmPassword) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '两次输入的密码不一致', 'data' => null], 400);
        }

        // 模型修改器自动哈希
        $user->password = $password;
        $user->save();

        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => null]);
    }

    /**
     * 上传当前登录用户的头像。
     */
    #[Route('/upload', 'POST')]
    public function upload(Request $request): JsonResponse
    {
        $user = User::find(satoken()->getCurrentLoginId());

        if (!$user) {
            return json(['errno' => 0, 'code' => 401, 'msg' => '用户不存在', 'data' => null], 401);
        }

        $file = $request->file('file');

        if (!$file || !$file->isValid()) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '请选择要上传的头像文件', 'data' => null], 400);
        }

        if (!str_starts_with($file->getMimeType(), 'image/')) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '仅支持上传图片文件', 'data' => null], 400);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '头像图片大小不能超过 2MB', 'data' => null], 400);
        }

        $path = storage('public')->putFile('avatar', $file);

        if ($path === false) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '头像上传失败', 'data' => null], 400);
        }

        $url          = storage('public')->url($path);
        $user->avatar = $url;
        $user->save();

        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => ['url' => $url]]);
    }

    /**
     * 用户列表：用户名 / 邮箱模糊搜索，角色与状态精确过滤。
     */
    public function index(
        int $current = 1,
        int $pageSize = 20,
        ?string $username = null,
        ?string $email = null,
        ?string $role = null,
        ?int $status = null,
    ): JsonResponse {
        $where = [];

        if ($username !== null && $username !== '') {
            $where['username_like'] = $username;
        }
        if ($email !== null && $email !== '') {
            $where['email_like'] = $email;
        }
        if ($role !== null && $role !== '') {
            $where['role'] = $role;
        }
        if ($status !== null) {
            $where['status'] = $status;
        }

        return $this->baseIndex($where, $current, $pageSize);
    }

    /**
     * 新建用户：基础校验后复用框架流程（密码经模型修改器自动哈希）。
     */
    public function save(Request $request): JsonResponse
    {
        $data = $request->post();

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '用户名、邮箱、密码不能为空', 'data' => null], 400);
        }

        if (!filter_var((string) $data['email'], FILTER_VALIDATE_EMAIL)) {
            return json(['errno' => 0, 'code' => 400, 'msg' => '邮箱格式不正确', 'data' => null], 400);
        }

        return $this->baseSave($request);
    }

    /**
     * 更新用户：密码留空表示不修改密码。
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return json(['errno' => 0, 'code' => 400, 'msg' => $this->notExistMessage, 'data' => null], 400);
        }

        $data = $request->post();

        if (isset($data['email']) && $data['email'] !== $user->email) {
            if (!filter_var((string) $data['email'], FILTER_VALIDATE_EMAIL)) {
                return json(['errno' => 0, 'code' => 400, 'msg' => '邮箱格式不正确', 'data' => null], 400);
            }

            $exists = User::where('email', $data['email'])->where('id', '<>', $id)->find();
            if ($exists) {
                return json(['errno' => 0, 'code' => 400, 'msg' => '邮箱已存在，请更换', 'data' => null], 400);
            }
        }

        // 密码为空则不更新
        if (!isset($data['password']) || $data['password'] === '') {
            unset($data['password']);
        }

        $user->save($data);

        return json(['errno' => 0, 'code' => 200, 'msg' => 'success', 'data' => $user]);
    }
}
