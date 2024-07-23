@ -0,0 +1,130 @@
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
}
}