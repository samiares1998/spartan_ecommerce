<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/toastify/toastify.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logo/logo1.svg') }}">

    @stack('css')

    <style>
        body::-webkit-scrollbar {
            display: none;
        }
        .bi{
            display:flex;
            align-items:center;
            justify-content:center;
        }
    </style>
        <style>
    /* Fondo general y texto */
    body {
        background-color: white !important;
        color: black !important;
    }

    /* Textos generales */
    h1, h2, h3, h4, h5, h6,
    p, span, a, label, small, strong {
        color: black !important;
    }

    /* Containers */


    /* Navbar y menús */
    .navbar, .dropdown-menu {
        background-color: white !important;
        color: black !important;
    }

    /* Formularios */
    .form-control, .form-select, textarea {
        background-color: white !important;
        color: black !important;
        border-color: #ced4da;
    }

    label {
        color: black !important;
    }

    /* Botones Bootstrap - texto blanco */
    .btn.btn-danger,
    .btn.btn-info,
    .btn.btn-primary,
    .btn.btn-secondary,
    .btn.btn-success,
    .btn.btn-warning {
        color: #fff !important;
    }

    /* bg-primary con opacidad Bootstrap */
    .bg-primary {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-primary-rgb), var(--bs-bg-opacity)) !important;
    }

    /* Responsive paddings */
    @media (min-width: 768px) {
        .p-md-3 {
            padding: 1rem !important;
        }
    }
    .btn-custom {
    background-color: #000000!important;
    color: #D4AF37 !important;
    border: none;
    transition: background-color 0.3s ease;
}

.btn-custom:hover {
    background-color: #1a1a1a !important;
}
.card:hover {
    border-color: #d4af37 !important; /* borde dorado */
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4); /* sombra dorada sutil */
    transition: all 0.3s ease;
}

.card:hover .product-title {
    color: #d4af37 !important; /* cambia el título a dorado al hacer hover */
}

</style>

</head>
<body>
  
{{ $slot }}



<script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/mazer.js') }}"></script>
<script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/vendors/toastify/toastify.js') }}"></script>
@if(session('success'))
    <script>
        Toastify({
            text: "{{session('success')}}",
            duration: 3000,
            close:true,
            gravity:"top",
            position: "right",
            backgroundColor: "#4fbe87",
        }).showToast();

   


    </script>
@endif

@stack('js')
@stack('scripts')



</body>
</html>