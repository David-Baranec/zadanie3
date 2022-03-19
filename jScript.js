function check_mail() {
    var mail_value = document.getElementById('e-mail').value;
    var reg = /^[A-Za-z0-9._%+-]{3,}@[a-zA-Z]{3,}([.]{1}[a-zA-Z]{2,}|[.]{1}[a-zA-Z]{2,}[.]{1}[a-zA-Z]{2,})$/;
    var c = document.getElementById("zlyMail");
    if (reg.test(mail_value) == false) {
      c.style.display = "block";
      return false;
    }
    else {    
      c.style.display = "none";
      return true;
    }
  }