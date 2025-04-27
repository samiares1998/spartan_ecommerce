@prepend('css')
<link rel="stylesheet" href="{{ asset('client/components/organisms/navbar/style.css') }}">
@endprepend
<header class="header" id="header">
  <nav class="nav container">
    <div class="nav-button">
      <div class="nav-toggle" id="nav-toggle">
        <i class="bi bi-list"></i>
      </div>
    </div>
    <a href="/" class="nav-logo" id="logo">
      <img src="{{ asset('shop/'.$path) }}" alt="">
    </a>
    <div class="nav-menu" id="nav-menu">
     <x-molecules.navbar.menu />
      <div class="nav-close" id="nav-close">
        <i class="bi bi-x"></i>
      </div>
    </div>
    <div class="icon-left">
      <x-molecules.navbar.search-bar/>
    </div>
  </nav>
</header>
@prepend('js')
  <script>
    const navMenu = document.getElementById("nav-menu"),
    navToggle = document.getElementById("nav-toggle"),
    navClose = document.getElementById("nav-close"),
    logo = document.getElementById("logo");

    if (navToggle) {
        navToggle.addEventListener("click", () => {
            navMenu.classList.add("show-menu");
        });
    }

    if (navClose) {
        navClose.addEventListener("click", () => {
            navMenu.classList.remove("show-menu");
        });
    }

    function Onfocus() {
        if (window.matchMedia("(min-width:767px)").matches) {
            navMenu.classList.add("d-none");
        } else {
            logo.classList.add("d-none");
        }
    }

    function Onblur() {
        if (window.matchMedia("(min-width:767px)").matches) {
            navMenu.classList.remove("d-none");
        } else {
            logo.classList.remove("d-none");
        }
    }
    // En tu archivo JavaScript principal o en un script en el layout
    document.addEventListener('DOMContentLoaded', function() {
        // Tiempo cuando se carga la página
        const pageLoadTime = performance.now();
        
        window.addEventListener('beforeunload', function() {
            // Calcula el tiempo total en segundos
            const totalTime = Math.round((performance.now() - pageLoadTime) / 1000);
            
            // Datos a enviar
            const analyticsData = new Blob([JSON.stringify({
                time_spent: totalTime,
                page_url: window.location.pathname,
                _token: document.querySelector('meta[name="csrf-token"]').content
            })], {type: 'application/json'});
            
            // Envía los datos
            navigator.sendBeacon('/track-time', analyticsData);
        });
    });
  </script>
@endprepend