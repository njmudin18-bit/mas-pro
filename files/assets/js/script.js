"use strict";

function capitalizeFirstLetter(text) {
  if (!text) {
    return text; // Mengembalikan string kosong/null/undefined jika inputnya kosong
  }
  
  // Ambil huruf pertama dan ubah menjadi kapital
  const firstLetter = text.charAt(0).toUpperCase();
  
  // Ambil sisa string (dari indeks 1) dan gabungkan
  const restOfString = text.slice(1);
  
  return firstLetter + restOfString;
}

function formatRupiahDecimal(value) {
  if (!value) return "0,00";
  return new Intl.NumberFormat('id-ID', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
  }).format(parseFloat(value));
}


function AllowNumberAndSlash(inputElement) {
  // Memastikan hanya angka dan garis miring yang diizinkan
  // Regex [^0-9/] artinya "karakter apa pun yang bukan 0-9 dan bukan /"
  inputElement.value = inputElement.value.replace(/[^0-9/]/g, '');
}

function AllowDecimal(input) {
  let value = input.value;
  value = value.replace(/[^0-9.,]/g, '');
  value = value.replace(/,/g, '.');
  let parts = value.split('.');
  if (parts.length > 2) {
    value = parts[0] + '.' + parts.slice(1).join('');
  }
  input.value = value;
}

// Buat fungsi untuk format angka
function formatRupiah(angka) {
  // pastikan angka berupa string
  angka = angka.toString().replace(/\D/g, ""); // hapus semua selain angka
  return angka.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // tambahkan titik tiap 3 digit
}

// Fungsi format tanggal ke 29 Juli, 2025
function formatTanggal(tgl) {
  const bulan = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
  ];
  const parts = tgl.split('-'); // [2025, 07, 29]
  const tahun = parts[0];
  const bulanNama = bulan[parseInt(parts[1]) - 1];
  const tanggal = parts[2];

  return `${tanggal} ${bulanNama}, ${tahun}`;
}

function formatTanggalWaktu(datetime) {
  if (!datetime) return "-"; // kalau kosong / null

  const bulan = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
  ];

  // Pisahkan tanggal dan waktu
  const parts = datetime.split(' '); 
  const tgl = parts[0];           // 2025-07-29
  const waktu = parts[1] ? parts[1].split('.')[0] : ''; // 13:28:01.000 → 13:28:01

  const tglParts = tgl.split('-');
  if (tglParts.length < 3) return datetime; // kalau format tidak sesuai

  const tahun = tglParts[0];
  const bulanNama = bulan[parseInt(tglParts[1]) - 1];
  const tanggal = tglParts[2];

  // Ambil hanya jam dan menit jika ada
  const jamMenit = waktu ? waktu.substring(0,5) : '';

  return jamMenit
    ? `${tanggal} ${bulanNama}, ${tahun} ${jamMenit}`
    : `${tanggal} ${bulanNama}, ${tahun}`;
}

// Fungsi hanya angka
function CheckNumeric() {
  var key = window.event.keyCode;
  return (key >= 48 && key <= 57) || key === 8 || key === 46 || key === 9;
}

// Fungsi format ribuan
function FormatCurrency(input) {
  let value = input.value.replace(/[^0-9]/g, ''); // Remove all non-digit characters
  if (value === '') {
    input.value = '';
    return;
  }

  // Format with comma as thousand separator
  input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function AllowDecimalAndCommaXXX(input) {
    let value = input.value.replace(/[^0-9.,]/g, ''); // Allow only numbers, dot, comma
    input.value = value;
}

function AllowDecimalAndCommaZZ(input) {
  // Ambil nilai input
  let value = input.value;

  // Hilangkan semua karakter selain angka dan koma
  value = value.replace(/[^0-9,]/g, '');

  // Pisahkan bagian integer dan desimal
  let parts = value.split(',');

  // Format bagian integer (sebelum koma)
  let integerPart = parts[0];
  let decimalPart = parts[1] !== undefined ? ',' + parts[1] : '';

  // Tambahkan titik ribuan
  let formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  // Gabungkan lagi
  input.value = formattedInteger + decimalPart;
}

function AllowDecimalAndCommaAA(input) {
  let value = input.value;
  const decimalSeparator = '.';
  const groupSeparator = ',';
  const maxDecimalDigits = 4;

  // Hilangkan semua karakter selain angka dan pemisah desimal
  let sanitizedValue = value.replace(new RegExp(`[^0-9${decimalSeparator}]`, 'g'), '');

  // Pastikan hanya ada satu pemisah desimal
  const decimalParts = sanitizedValue.split(decimalSeparator);
  if (decimalParts.length > 2) {
    sanitizedValue = decimalParts[0] + decimalSeparator + decimalParts.slice(1).join('');
  }

  let integerPart = decimalParts[0];
  let decimalPart = decimalParts.length > 1 ? decimalSeparator + decimalParts[1].slice(0, maxDecimalDigits) : '';

  // Format bagian integer dengan pemisah ribuan
  let formattedInteger = new Intl.NumberFormat('en-US').format(integerPart);

  input.value = formattedInteger + decimalPart;
}

function AllowDecimalAndComma(input) {
  let value = input.value;
  const decimalSeparator = ',';
  const groupSeparator = '.';
  const maxDecimalDigits = 4;

  // Hilangkan semua karakter selain angka dan pemisah desimal
  let sanitizedValue = value.replace(new RegExp(`[^0-9${decimalSeparator}]`, 'g'), '');

  // Pastikan hanya ada satu pemisah desimal
  const decimalParts = sanitizedValue.split(decimalSeparator);
  if (decimalParts.length > 2) {
    sanitizedValue = decimalParts[0] + decimalSeparator + decimalParts.slice(1).join('');
  }

  let integerPart = decimalParts[0];
  let decimalPart = decimalParts.length > 1 ? decimalSeparator + decimalParts[1].slice(0, maxDecimalDigits) : '';

  // Format bagian integer dengan pemisah ribuan (menggunakan 'de-DE' untuk titik)
  let formattedInteger = new Intl.NumberFormat('de-DE').format(integerPart);

  input.value = formattedInteger + decimalPart;
}

function isNumber(evt) {
  evt = (evt) ? evt : window.event;
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
  }

  return true;
}

function openInNewTab(url) {
  window.open(url, '_blank').focus();
}

function capitalizeFirstLetter(str) {
  const capitalized = str.charAt(0).toUpperCase() + str.slice(1);

  return capitalized;
}

$(document).ready(function () {
    $(".card-header-right .close-card").on("click", function () {
        var $this = $(this);
        $this.parents(".card").animate({ opacity: "0", "-webkit-transform": "scale3d(.3, .3, .3)", transform: "scale3d(.3, .3, .3)" });
        setTimeout(function () {
            $this.parents(".card").remove();
        }, 800);
    });
    $(".card-header-right .reload-card").on("click", function () {
        var $this = $(this);
        $this.parents(".card").addClass("card-load");
        $this.parents(".card").append('<div class="card-loader"><i class="feather icon-radio rotate-refresh"></div>');
        setTimeout(function () {
            $this.parents(".card").children(".card-loader").remove();
            $this.parents(".card").removeClass("card-load");
        }, 3000);
    });
    $(".card-header-right .card-option .open-card-option").on("click", function () {
        var $this = $(this);
        if ($this.hasClass("icon-x")) {
            $this.parents(".card-option").animate({ width: "30px" });
            $this.parents(".card-option").children("li").children(".open-card-option").removeClass("icon-x").fadeIn("slow");
            $this.parents(".card-option").children("li").children(".open-card-option").addClass("icon-chevron-left").fadeIn("slow");
            $this.parents(".card-option").children(".first-opt").fadeIn();
        } else {
            $this.parents(".card-option").animate({ width: "130px" });
            $this.parents(".card-option").children("li").children(".open-card-option").addClass("icon-x").fadeIn("slow");
            $this.parents(".card-option").children("li").children(".open-card-option").removeClass("icon-chevron-left").fadeIn("slow");
            $this.parents(".card-option").children(".first-opt").fadeOut();
        }
    });
    $(".card-header-right .minimize-card").on("click", function () {
        var $this = $(this);
        var port = $($this.parents(".card"));
        var card = $(port).children(".card-block").slideToggle();
        $(this).toggleClass("icon-minus").fadeIn("slow");
        $(this).toggleClass("icon-plus").fadeIn("slow");
    });
    $(".card-header-right .full-card").on("click", function () {
        var $this = $(this);
        var port = $($this.parents(".card"));
        port.toggleClass("full-card");
        $(this).toggleClass("icon-minimize");
        $(this).toggleClass("icon-maximize");
    });
    $("#more-details").on("click", function () {
        $(".more-details").slideToggle(500);
    });
    $(".mobile-options").on("click", function () {
        $(".navbar-container .nav-right").slideToggle("slow");
    });
    $(".search-btn").on("click", function () {
        $(".main-search").addClass("open");
        $(".main-search .form-control").animate({ width: "200px" });
    });
    $(".search-close").on("click", function () {
        $(".main-search .form-control").animate({ width: "0" });
        setTimeout(function () {
            $(".main-search").removeClass("open");
        }, 300);
    });
    $("#styleSelector .style-cont").slimScroll({ setTop: "1px", height: "calc(100vh - 480px)" });
    var a = $(window).height() - 80;
    $(".main-friend-list").slimScroll({ height: a, allowPageScroll: false, wheelStep: 5 });
    var a = $(window).height() - 155;
    $(".main-friend-chat").slimScroll({ height: a, allowPageScroll: false, wheelStep: 5 });
    $("#search-friends").on("keyup", function () {
        var g = $(this).val().toLowerCase();
        $(".userlist-box .media-body .chat-header").each(function () {
            var s = $(this).text().toLowerCase();
            $(this).closest(".userlist-box")[s.indexOf(g) !== -1 ? "show" : "hide"]();
        });
    });
    $(".displayChatbox").on("click", function () {
        var my_val = $(".pcoded").attr("vertical-placement");
        if (my_val == "right") {
            var options = { direction: "left" };
        } else {
            var options = { direction: "right" };
        }
        $(".showChat").toggle("slide", options, 500);
    });
    $(".userlist-box").on("click", function () {
        var my_val = $(".pcoded").attr("vertical-placement");
        if (my_val == "right") {
            var options = { direction: "left" };
        } else {
            var options = { direction: "right" };
        }
        $(".showChat_inner").toggle("slide", options, 500);
    });
    $(".back_chatBox").on("click", function () {
        var my_val = $(".pcoded").attr("vertical-placement");
        if (my_val == "right") {
            var options = { direction: "left" };
        } else {
            var options = { direction: "right" };
        }
        $(".showChat_inner").toggle("slide", options, 500);
        $(".showChat").css("display", "block");
    });
    $(".back_friendlist").on("click", function () {
        var my_val = $(".pcoded").attr("vertical-placement");
        if (my_val == "right") {
            var options = { direction: "left" };
        } else {
            var options = { direction: "right" };
        }
        $(".p-chat-user").toggle("slide", options, 500);
        $(".showChat").css("display", "block");
    });
    $('[data-toggle="tooltip"]').tooltip();
    Waves.init();
    Waves.attach(".flat-buttons", ["waves-button"]);
    Waves.attach(".float-buttons", ["waves-button", "waves-float"]);
    Waves.attach(".float-button-light", ["waves-button", "waves-float", "waves-light"]);
    Waves.attach(".flat-buttons", ["waves-button", "waves-float", "waves-light", "flat-buttons"]);
    $(".form-control").on("blur", function () {
        if ($(this).val().length > 0) {
            $(this).addClass("fill");
        } else {
            $(this).removeClass("fill");
        }
    });
    $(".form-control").on("focus", function () {
        $(this).addClass("fill");
    });
    $("#mobile-collapse i").addClass("icon-toggle-right");
    $("#mobile-collapse").on("click", function () {
        $("#mobile-collapse i").toggleClass("icon-toggle-right");
        $("#mobile-collapse i").toggleClass("icon-toggle-left");
    });
});
$(document).ready(function () {
    var $window = $(window);
    $(".loader-bg").fadeOut();
});
function toggleFullScreen() {
    var a = $(window).height() - 10;
    if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
    $(".full-screen").toggleClass("icon-maximize");
    $(".full-screen").toggleClass("icon-minimize");
}
