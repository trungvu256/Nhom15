
<?php
session_start();
// session_destroy();
// die();
ob_start();
if (!isset($_SESSION['giohang'])) {
    $_SESSION['giohang'] = [];
}

include "dao/pdo.php";
include "view/header.php";
include "dao/danhmuc.php";
include "dao/sanpham.php";
include "dao/giohang.php";
include "dao/user.php";
include "dao/donhang.php";
include "dao/binhluan.php";
include "dao/voucher.php";
include "dao/mail.php";
include "dao/convnpay.php";
//data danh cho trang chu
$dssp_all = selectall_sanpham();
$dssp_new = get_dssp_newtrangchu(4);
$dssp_best = get_dssp_best(4);
$dssp_caphe = get_sp_caphe(4);
$dssp_tra = get_sp_tra(4);
$dssp_banhmi = get_sp_banhmi(4);
if (!isset($_GET['pg'])) {
    include "view/home.php";
} else {
    switch ($_GET['pg']) {
case 'dangnhap':
    switch ($_GET['pg']) {
            include "view/dangnhap.php";
            if (isset($_POST['submit']) && ($_POST["submit"])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $kq = checkuser($username, $password);

                if (is_array($kq) && (count($kq))) {
                    $_SESSION['s_user'] = $kq;
                    header('location:index.php');
                } else {
                    $tb = "Tài khoản không tồn tại khoặc sai";
                    $_SESSION['tb_dangnhap'] = $tb;
                    header('location:index.php?pg=dangnhap');
                }
            }
            break;
        case 'dangky':
            include "view/dangky.php";
            break;
        case 'adduser':
            if (isset($_POST['submit']) && ($_POST["submit"])) {
                $password = $_POST['password'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                if (checkuseremail($email) == []) {
                    $iduser = user_insert_id($username, $password, $email);
                    usernh_insert_id($iduser, '', '');
                    header('location:index.php?pg=dangnhap');
                } else {
                    $_SESSION['error_email'] = 'Email đã tồn tại!';
                    header('location:index.php?pg=dangky');
                }
            }
            break;
        case 'dangxuat':
            if (isset($_SESSION['s_user']) && ($_SESSION['s_user'] > 0)) {
                unset($_SESSION['s_user']);
                header('location:index.php');
            }
            header('location:index.php');
            break;
        case 'quenmk':
            if (isset($_POST['guiemail']) && ($_POST['guiemail'])) {
                $email = $_POST['email'];
                $checkemail = kiemtraemail($email);
                if (is_array($checkemail)) {
                    submit_mailpass($checkemail['password'], $checkemail['email']);
                    echo '<script>alert("Chúng tôi đã cấp cho bạn mật khẩu vui lòng check mail của bạn.")</script>';
                    echo '<script>window.location.href = "index.php?pg=dangnhap";</script>';
                } else {
                    $thongbao = "Email này không tồn tại!";
                }
            }
            include "view/quenmk.php";
            break;
        case 'myaccount':
            if (isset($_SESSION['s_user']) && ($_SESSION['s_user'] > 0)) {
                include "view/myaccount.php";
            }
            break;
        case 'thaydoimatkhau':
            if (isset($_SESSION['s_user']) && ($_SESSION['s_user'] > 0)) {
                include "view/thaydoimatkhau.php";
            }
            break;
        case 'updatauser':
            if (isset($_POST['capnhat']) && ($_POST["capnhat"])) {
                $username = $_POST['username'];

                $email = $_POST['email'];
                $dienthoai = $_POST['dienthoai'];
                $diachi = $_POST['diachi'];
                $id = $_POST['id'];
                $img = $_FILES['image'];
                if ($img['name'] != '') {
                    $imgold = './uploads/' . $_SESSION['s_user']['anh'];
                    if (is_file($imgold)) {
                        unlink($imgold);
                    }
                    $target_file = './uploads/' . $img['name'];
                    move_uploaded_file($img['tmp_name'], $target_file);
                }
                // echo '<pre>';
                // print_r($img);
                // die();
                if ($email != $_SESSION['s_user']['email']) {
                    if (checkuseremail($email) != []) {
                        $_SESSION['error_email'] = 'Email đã tồn tại!';
                        header('location:index.php?pg=myaccount');
                    } else {
                        user_update($username, $email, $diachi, $dienthoai, $id,$img['name']);
                    }
                } else {
                    user_update($username, $email, $diachi, $dienthoai, $id,$img['name']);
                }
            }
            include "view/myaccount_confirm.php";
            break;

            case 'donhang':
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($_SESSION['s_user']) && $_SESSION['giohang'] != []) {
                        $a = noinhanhang($_SESSION['s_user']['id']);
                        foreach ($a as $value) {
                            extract($value);
                            $nguoinhan_diachi = $noinhan;
                            $nguoinhan_tel = $sdt;
                        }
                        if($nguoinhan_diachi == ''||$nguoinhan_tel==''){
                            update_noinhan($_SESSION['s_user']['id'], $_SESSION['s_user']['dienthoai'], $_SESSION['s_user']['diachi']);
                            $nguoinhan_diachi = $_SESSION['s_user']['diachi'];
                            $nguoinhan_tel = $_SESSION['s_user']['dienthoai'];
                        }
                        $nguoinhan_ten = $_SESSION['s_user']['username'];
                        $pttt = $_POST['pttt'];
                        $ghichu = isset($_SESSION['ghichu']) ? $_SESSION['ghichu'] : '';
                        $iduser = $_SESSION['s_user']['id'];
                        $_SESSION['madon'] = "xshop" . $iduser . "-" . date("His-dmY");
                        $id_trangthai = 1;
                        $total = get_tongdonhang();
                        $ship = 0;
                        $voucher = $_POST['tienvouch'];
                        $tongthanhtoan =  $_POST['tongtien'];
                        $idvoucher = $_POST['idvouch'];
                        date_default_timezone_set('Asia/Ho_Chi_Minh');
                        $ngaymua = date("Y-m-d H:i:s");
                        $_SESSION['bill']=[
                            $_SESSION['madon'],
                            $iduser,
                            $total,
                            $pttt,
                            $ghichu,
                            $nguoinhan_ten,
                            $nguoinhan_diachi,
                            $nguoinhan_tel,
                            $ship,
                            $tongthanhtoan,
                            $voucher,
                            $id_trangthai,
                            $ngaymua,
                            $idvoucher];
    
                        if ($pttt == 2) {
                            $vnp_OrderInfo = "Thanh toán đơn hàng đặt tại Coffe chòi";
                            $vnp_OrderType = "Billpayment";
                            $vnp_Amount = $tongthanhtoan * 100; 
                            $vnp_Locale = "VN";
                            $vnp_BankCode = "NCB";
                            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
                            $vnp_ExpireDate = $expire;
                            $vnp_TxnRef = $_SESSION['madon'];
                           
                            $inputData = array(
                                "vnp_Version" => "2.1.0",
                                "vnp_TmnCode" => $vnp_TmnCode,
                                "vnp_Amount" => $vnp_Amount,
                                "vnp_Command" => "pay",
                                "vnp_CreateDate" => date('YmdHis'),
                                "vnp_CurrCode" => "VND",
                                "vnp_IpAddr" => $vnp_IpAddr,
                                "vnp_Locale" => $vnp_Locale,
                                "vnp_OrderInfo" => $vnp_OrderInfo,
                                "vnp_OrderType" => $vnp_OrderType,
                                "vnp_ReturnUrl" => $vnp_Returnurl,
                                "vnp_TxnRef" => $vnp_TxnRef,
                                "vnp_ExpireDate" => $vnp_ExpireDate
                            );
    
                            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                                $inputData['vnp_BankCode'] = $vnp_BankCode;
                            }
    
                           
    
                            ksort($inputData);
                            $query = "";
                            $i = 0;
                            $hashdata = "";
                            foreach ($inputData as $key => $value) {
                                if ($i == 1) {
                                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                                } else {
                                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                                    $i = 1;
                                }
                                $query .= urlencode($key) . "=" . urlencode($value) . '&';
                            }
    
                            $vnp_Url = $vnp_Url . "?" . $query;
                            if (isset($vnp_HashSecret)) {
                                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
                                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                            }
                            $returnData = array(
                                'code' => '00',
                                'message' => 'success',
                                'data' => $vnp_Url
                            );
    
                            if (isset($_POST['submit'])) {
                            
                                header("location:" . $vnp_Url);
                              
                                die();
                            } else {
                                echo json_encode($returnData);
                            }
                        }
                        if ($pttt == 3) {
                            
                            header('Content-type: text/html; charset=utf-8');
                            
                            function execPostRequest($url, $data)
                            {
                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt(
                                    $ch,
                                    CURLOPT_HTTPHEADER,
                                    array(
                                        'Content-Type: application/json',
                                        'Content-Length: ' . strlen($data)
                                    )
                                );
                                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                                //execute post
                                $result = curl_exec($ch);
                                //close connection
                                curl_close($ch);
                                return $result;
                            }
        
        
                            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        
        
                            $partnerCode = 'MOMOBKUN20180529';
                            $accessKey = 'klm05TvNBzhg7h7j';
                            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        
                            $orderInfo = "Thanh toán qua MoMo";
                            $amount = $tongthanhtoan;
                            $orderId =  $_SESSION['madon'] ;
                            // $orderId = time() . "";
                            $redirectUrl = "http://localhost/duan1/index.php?pg=billconfirm";
                            $ipnUrl = "http://localhost/duan1/index.php?pg=billconfirm";
                            $extraData = "";
        
                            $requestId = time() . "";
                            $requestType = "payWithATM";
                            $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        
                            //before sign HMAC SHA256 signature
                            $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
                            $signature = hash_hmac("sha256", $rawHash, $secretKey);
                            $data = array(
                                'partnerCode' => $partnerCode,
                                'partnerName' => "Test",
                                "storeId" => "MomoTestStore",
                                'requestId' => $requestId,
                                'amount' => $amount,
                                'orderId' => $orderId,
                                'orderInfo' => $orderInfo,
                                'redirectUrl' => $redirectUrl,
                                'ipnUrl' => $ipnUrl,
                                'lang' => 'vi',
                                'extraData' => $extraData,
                                'requestType' => $requestType,
                                'signature' => $signature
                            );
                            $result = execPostRequest($endpoint, json_encode($data));
                            $jsonResult = json_decode($result, true);  
        
                            header('Location: ' . $jsonResult['payUrl']);
    
                        }
                        
                        if ($pttt !=2 && $pttt!=3 ) {
                            header("Location:index.php?pg=billconfirm");
                        }
                    } else {
                        header('location:index.php?pg=viewcart');
                    }
                } else {
                  
    
                    include "view/donhang.php";
                }
    
                break;
}
}