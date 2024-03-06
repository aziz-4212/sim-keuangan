<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SIM Front Office | RSI Kendal</title>

<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet"
    href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
<!-- iCheck -->
<link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
<!-- summernote -->
<link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-bs4.min.css') }}">

<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<!-- Datepicker -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ICO') }}" />
<style>
    *{
        font-size: 12px;
    }
    @media print {
        .modal-footer {
            display: none;
            /* Sembunyikan modal footer saat mencetak */
        }

        .nav.nav-tabs {
            display: none;
        }

        .bg-tr-color {
            background-color: #2b2b2b;
        }
    }

    .error-template {
        padding: 40px 15px;
        text-align: center;
    }

    .error-actions {
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .invisible-border {
        border: none;
        outline: none;
        resize: none;
        overflow-y: hidden;
        overflow-x: hidden;
        /* Menghilangkan sudut kanan bawah (resize handle) */
        /* (Opsional) Mengatur warna latar belakang untuk menghindari tampilan yang terlihat transparan pada beberapa browser */
        background-color: transparent;
    }

    .dynamic-textarea[disabled] {
        background-color: transparent;
        padding: 0;
        margin: 0
            /* atau ganti dengan warna putih jika diinginkan */
    }

    .table-border-utama {
        border: 1px solid #e9e9e9;
    }

    .dynamic-textarea[readonly] {
        background-color: transparent;
        padding: 0;
        margin: 0
            /* atau ganti dengan warna putih jika diinginkan */
    }

    @keyframes blink {
        50% {
            background-color: red;
        }
    }

    .blink {
        animation: blink 1s infinite;
    }

    .fixed-detail {
        position: absolute;
        top: 10px;
        /* Sesuaikan dengan jarak dari atas yang diinginkan */
        width: 100%;
    }

    .sticky-tabs {
        position: sticky;
        top: 0;
        background-color: #fff;
        /* Atur warna latar belakang sesuai kebutuhan */
        z-index: 1000;
        /* Atur z-index agar tetap di atas konten */
    }

    .scrollbody {
        overflow-y: auto;
        /* Membuat konten menjadi scrollable */
        height: 85vh;
        /* Tentukan tinggi konten sesuai kebutuhan */
    }

    @media screen and (max-width: 1780px) {
        .scrollbody {
            height: 79vh;
            /* Contoh pengaturan tinggi yang disesuaikan dengan tinggi menu di atasnya */
        }
    }

    @media screen and (max-width: 1680px) {
        .scrollbody {
            height: 77vh;
            /* Contoh pengaturan tinggi yang disesuaikan dengan tinggi menu di atasnya */
        }
    }

    @media screen and (max-width: 1580px) {
        .scrollbody {
            height: 75vh;
            /* Contoh pengaturan tinggi yang disesuaikan dengan tinggi menu di atasnya */
        }
    }

    @media screen and (max-width: 1480px) {
        .scrollbody {
            height: 73vh;
            /* Contoh pengaturan tinggi yang disesuaikan dengan tinggi menu di atasnya */
        }
    }

    @media screen and (max-width: 1366px) {
        .scrollbody {
            height: 68vh;
            /* Contoh pengaturan tinggi yang disesuaikan dengan tinggi menu di atasnya */
        }
    }

    @media screen and (max-width: 1366px) {
        .scrollbody {
            height: 68vh;
            /* Contoh pengaturan tinggi yang disesuaikan dengan tinggi menu di atasnya */
        }
    }

    /* Menyesuaikan .scrollbody pada layar dengan lebar 1366px atau lebih besar */
    @media screen and (max-width: 1366px) {
        .scrollbody {
            height: 68vh;
            /* Contoh pengaturan tinggi yang disesuaikan dengan tinggi menu di atasnya */
        }
    }

    @media (max-width: 768px) {
        .scrollbody {
            height: 50vh;
            /* Sesuaikan tinggi konten untuk layar kecil */
        }
    }
</style>

<style>
    img.scanned {
        height: 200px;
        /** Sets the display size */
        margin-right: 12px;
    }

    div#images {
        margin-top: 20px;
    }
</style>
