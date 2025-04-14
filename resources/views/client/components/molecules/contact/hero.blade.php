@push('css')
<style>
  .form-container {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  .form-container h2 {
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .form-container label {
    font-weight: 500;
  }

  .form-control, .form-select {
    border-radius: 0.5rem;
  }

  .btn-primary {
    background-color: #25396f;
    border: none;
    border-radius: 0.5rem;
    padding: 0.6rem 2rem;
    font-weight: 500;
  }

  .btn-primary:hover {
    background-color: #1a2b51;
  }
</style>
@endpush

<div class="py-md-5 py-2">
  <div class="container about-text">
    <h1 class="font-primary text-center mt-5">Queremos escucharte</h1>

    <div class="form-container mt-5">
      <h2 class="text-center text-primary">¿Tienes algo que contarnos?</h2>
      <form  action="{{ route('contactForm') }}" method="post" enctype="multipart/form-data">
      @csrf
        <div class="mb-3">
          <label for="fname" class="form-label">Nombre</label>
          <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Tu nombre..." required>
        </div>

        <div class="mb-3">
          <label for="lname" class="form-label">Apellido</label>
          <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Tu apellido..." required>
        </div>

        <div class="mb-3">
          <label for="lname" class="form-label">Correo</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Tu correo..." required>
        </div>

        <div class="mb-3">
          <label for="country" class="form-label">País</label>
          <select id="country" name="country" class="form-select">
            <option value="colombia">Colombia</option>
            <option value="mexico">México</option>
            <option value="argentina">Argentina</option>
            <option value="otros">Otro</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="subject" class="form-label">Mensaje</label>
          <textarea id="subject" name="subject" class="form-control" placeholder="Déjanos tu mensaje o sugerencia..." rows="6"></textarea>
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>
