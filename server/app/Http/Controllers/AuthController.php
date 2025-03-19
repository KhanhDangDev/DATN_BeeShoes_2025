<?php

namespace App\Http\Controllers;

use App\Constants\CommonStatus;
use App\Constants\ConstantSystem;
use App\Constants\Role as ConstantsRole;
use App\Exceptions\NotFoundException;
use App\Exceptions\RestApiException;
use App\Helpers\ApiResponse;
use App\Helpers\CustomCodeHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountRequestBody;
use App\Http\Resources\Accounts\AccountResource;
use App\Jobs\SendEmailCreateCustomer;
use App\Jobs\SendEmailVerification;
use App\Mail\VerifyEmail;
use App\Models\Account;
use App\Models\Cart;
use App\Models\CartDetails;
use App\Models\Notification;
use App\Models\ProductDetails;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $cartItems = $request->cartItems;

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'nullable|string',
        ]);

        $credentials = request(['email', 'password']);

        $roleCustomer = Role::where('code', ConstantsRole::CUSTOMER)->first();
        $account = Account::where('email', $credentials['email'])
            ->whereIn('role_id', [$roleCustomer->id])
            ->first();

        if (!$account) {
            return ApiResponse::responseError(
                ConstantSystem::UNAUTHORIZED_CODE,
                ConstantSystem::UNAUTHORIZED,
                "Tài khoản hoặc mật khẩu không chính xác"
            );
        }

        if ($account->email_verified_at === null) {
            $data['user'] = $account;
            $data['email'] = $account->email;
            SendEmailVerification::dispatch($data)->delay(now()->addSeconds(3));
            return ApiResponse::responseError(
                ConstantSystem::UNAUTHORIZED_CODE,
                ConstantSystem::UNAUTHORIZED,
                'Bạn chưa xác thực tài khoản của mình. Chúng tôi đã gửi cho bạn email kích hoạt tài khoản, vui lòng kiểm tra email của bạn',
            );
        }

        if ($account && Hash::check($credentials['password'], $account->password)) {
            $token = auth()->login($account);
            return $this->createNewToken($token, $cartItems);
        } else {
            return ApiResponse::responseError(
                ConstantSystem::UNAUTHORIZED_CODE,
                ConstantSystem::UNAUTHORIZED,
                "Tài khoản hoặc mật khẩu không chính xác"
            );
        }
    }

    public function loginAdmin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'nullable|string',
        ]);

        $credentials = request(['email', 'password']);

        $roleAdmin = Role::where('code', ConstantsRole::ADMIN)->first();
        $roleEmp = Role::where('code', ConstantsRole::EMPLOYEE)->first();
        $account = Account::where('email', $credentials['email'])
            ->whereIn('role_id', [$roleAdmin->id, $roleEmp->id])
            ->first();

        if (!$account) {
            return ApiResponse::responseError(
                ConstantSystem::UNAUTHORIZED_CODE,
                ConstantSystem::UNAUTHORIZED,
                "Tài khoản hoặc mật khẩu không chính xác"
            );
        }

        if ($account->status === CommonStatus::UN_ACTIVE) {
            return ApiResponse::responseError(
                ConstantSystem::UNAUTHORIZED_CODE,
                ConstantSystem::UNAUTHORIZED,
                "Tài khoản hoặc mật khẩu không chính xác"
            );
        }

        if ($account && Hash::check($credentials['password'], $account->password)) {
            $token = auth()->login($account);
            return $this->createNewTokenAdmin($token);
        } else {
            return ApiResponse::responseError(
                ConstantSystem::UNAUTHORIZED_CODE,
                ConstantSystem::UNAUTHORIZED,
                "Tài khoản hoặc mật khẩu không chính xác"
            );
        }
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        $account = Account::query();
        $prefix = ConstantSystem::CUSTOMER_CODE_PREFIX;

        $roleCustomer = Role::where('code', ConstantsRole::CUSTOMER)->first();

        $findEmailCustomer = Account::where('email', $validated['email'])
            ->where('role_id', $roleCustomer->id)->first();

        if ($findEmailCustomer) {
            throw new RestApiException("Địa chỉ email này đã tồn tại");
        }

        try {
            DB::beginTransaction();

            $user = Account::create([
                'email' => $validated['email'],
                'code' => CustomCodeHelper::generateCode($account, $prefix),
                'role_id' => $roleCustomer->id,
                'status' => CommonStatus::UN_ACTIVE,
                'password' => bcrypt($validated['password']),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new RestApiException($e->getMessage());
            // throw new RestApiException("Đăng ký tài khoản không thành công");
        }

        $data['user'] = $user;
        $data['email'] = $validated['email'];
        SendEmailVerification::dispatch($data)->delay(now()->addSeconds(3));

        return ApiResponse::responseObject($user, "Đăng ký thành công!", ConstantSystem::SUCCESS_CODE);
    }

    public function resetPasswordAdmin(Request $req)
    {
        $account = Account::where('email', $req->email)->first();
        if (!$account) {
            throw new RestApiException("Email không tồn tại");
        }

        try {
            DB::beginTransaction();

            $length = 12;
            $pass = Str::random($length, 'aA0');
            $account->password = bcrypt($pass);
            $account->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // throw new RestApiException("Đổi mật khẩu không thành công");
            throw new RestApiException($e->getMessage());
        }
        SendEmailCreateCustomer::dispatch($account, $pass, "reset");

        return ApiResponse::responseObject(new AccountResource($account));
    }

    public function changePasswordAdmin(Request $req)
    {
        $account = Account::find($req->id);
        if (!$account) {
            throw new NotFoundException("Tài khoản không tồn tại");
        }

        try {
            DB::beginTransaction();
            if (Hash::check($req->password, $account->password)) {
                $account->password = bcrypt($req->newPassword);
                $account->save();
            } else {
                throw new RestApiException("Mật khẩu hiện tại không chính xác");
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // throw new RestApiException("Đổi mật khẩu không thành công");
            throw new RestApiException($e->getMessage());
        }

        return ApiResponse::responseObject(new AccountResource($account));
    }

    public function verify($id)
    {
        $roleCustomer = Role::where('code', ConstantsRole::CUSTOMER)->first();

        $customer = Account::where('id', $id)->where('role_id', $roleCustomer->id)->first();

        if (!$customer) {
            throw new NotFoundException("Tài khoản không tồn tại");
        }

        if ($customer->email_verified_at !== null) {
            throw new NotFoundException("Tài khoản đã được xác thực");
        }

        try {
            DB::beginTransaction();

            $customer->email_verified_at = now();
            $customer->status = CommonStatus::IS_ACTIVE;
            $customer->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new RestApiException("Xác thực tài khoản không thành công");
        }

        // login
        return ApiResponse::responseObject(new AccountResource($customer), "Xác thực tài khoản thành công!", ConstantSystem::SUCCESS_CODE);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function show()
    {
        $response = Auth::user();
        $cartItemsByCart = CartDetails::getCartItemsByAccount(Auth::user()->id);
        $response['cartItems'] = $cartItemsByCart;
        return ApiResponse::responseObject(new AccountResource($response));
    }

    public function showAdmin()
    {
        $response = Auth::user();
        $role = Role::find(Auth::user()->role_id)->code;
        $response['role'] = $role;
        return ApiResponse::responseObject(new AccountResource($response));
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        if ($user->phone_number !== $request->phoneNumber) {
            $roleCustomer = Role::where('code', ConstantsRole::CUSTOMER)->first();
            $accountPhoneNumner = Account::where('phone_number', $request->phoneNumber)
                ->where('role_id', $roleCustomer->id)
                ->first();

            if ($accountPhoneNumner) {
                throw new RestApiException("SĐT này đã tồn tại");
            }
        }

        try {
            DB::beginTransaction();

            $user->full_name = $request->fullName;
            $user->phone_number = $request->phoneNumber;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new RestApiException($e->getMessage());
        }

        return ApiResponse::responseObject(new AccountResource($user));
    }

    public function showAccountRegister($id)
    {
        $roleCustomer = Role::where('code', ConstantsRole::CUSTOMER)->first();

        $customer = Account::where('id', $id)->where('role_id', $roleCustomer->id)->first();

        if (!$customer) {
            throw new NotFoundException("Tài khoản không tồn tại");
        }

        if ($customer->email_verified_at !== null) {
            throw new NotFoundException("Tài khoản đã được xác thực");
        }

        return ApiResponse::responseObject(new AccountResource($customer));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return ApiResponse::responseObject([], 'Đăng xuất thành công!', ConstantSystem::SUCCESS_CODE);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token, $cartItems)
    {
        $id = Auth::user()->id;
        $isRemoveCartLocalStrorageBrowser = false;
        $cartItemsByCart = [];

        $cartByAccount = Cart::where('account_id', $id)->first();

        try {
            DB::beginTransaction();

            if (!$cartByAccount) {
                $newCart = new Cart();
                $newCart->account_id = $id;
                $newCart->save();

                if (count($cartItems) > 0) {
                    foreach ($cartItems as $cartItem) {

                        $findProductItem = ProductDetails::find($cartItem['id']);

                        if ($findProductItem) {
                            $newCartItem = new CartDetails();
                            $newCartItem->cart_id = $newCart->id;
                            $newCartItem->quantity = $cartItem['quantity'];
                            $newCartItem->product_details_id = $cartItem['id'];
                            $newCartItem->created_at = Carbon::parse($cartItem['createdAt']);
                            $newCartItem->save();
                        }
                    }
                    $isRemoveCartLocalStrorageBrowser = true;
                    $cartItemsByCart = CartDetails::getCartItemsByAccount($id);
                }
            } else {
                $cartItemsByCart = CartDetails::getCartItemsByAccount($id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $isRemoveCartLocalStrorageBrowser = false;
            throw new RestApiException($e->getMessage());
        }

        $response['accessToken'] = $token;
        $response['user'] = auth()->user();
        $response['user']['cartItems'] = $cartItemsByCart;
        $response['isRemoveCartBrowser'] = $isRemoveCartLocalStrorageBrowser;

        return ApiResponse::responseObject($response);
    }

    protected function createNewTokenAdmin($token)
    {
        $role = Role::find(Auth::user()->role_id)->code;
        $response['accessToken'] = $token;
        $response['user'] = new AccountResource(auth()->user());
        $response['user']['role'] = $role;
        return ApiResponse::responseObject($response);
    }
}
