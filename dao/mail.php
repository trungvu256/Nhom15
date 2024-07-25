<?php

include "mail/sendmail.php";

// Mail lấy pass
function submit_mailpass($pass, $mail)
{
  $maildathang = $mail;
  $tieude = "Đây là mail lấy lại mật khẩu !";
  $noidung = "";

  $noidung .= 
  $mail = new Mailer();
  $mail->dathangmail($maildathang, $tieude, $noidung,);
}
