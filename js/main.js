document.addEventListener("DOMContentLoaded", () => {
  const authArea = document.querySelector("[data-auth-area]");

  async function cargarSesion() {
    try {
      const respuesta = await fetch("../php/usuario.php", {
        credentials: "same-origin"
      });

      const usuario = await respuesta.json();

      if (authArea) {
        authArea.replaceChildren();

        if (usuario.logueado) {
          const saludo = document.createElement("span");
          saludo.className = "auth-greeting";
          saludo.textContent = `Hola, ${usuario.nombre_usuario}`;

          const cuenta = document.createElement("a");
          cuenta.href = "mi-cuenta.html";
          cuenta.textContent = "Mi cuenta";

          const salir = document.createElement("a");
          salir.href = "../php/logout.php";
          salir.textContent = "Cerrar sesión";

          authArea.append(saludo, cuenta, salir);
        } else {
          const entrar = document.createElement("a");
          entrar.href = "login.html";
          entrar.textContent = "Iniciar sesión";

          const registro = document.createElement("a");
          registro.href = "registro.html";
          registro.textContent = "Crear cuenta";

          authArea.append(entrar, registro);
        }
      }

      const profileName = document.querySelector("[data-profile-name]");
      const profileEmail = document.querySelector("[data-profile-email]");

      if (profileName) {
        profileName.textContent = usuario.logueado
          ? usuario.nombre_usuario
          : "No iniciaste sesión";
      }

      if (profileEmail) {
        profileEmail.textContent = usuario.logueado
          ? usuario.email
          : "—";
      }

      const gate = document.querySelector("[data-community-gate]");
      const layout = document.querySelector("[data-community-layout]");

      if (gate && layout) {
        gate.hidden = !!usuario.logueado;
        layout.hidden = !usuario.logueado;
      }

      return usuario;
    } catch (error) {
      console.error("No se pudo consultar la sesión:", error);
      return { logueado: false };
    }
  }

  function mostrarAvisoFormulario() {
    const params = new URLSearchParams(location.search);
    const error = params.get("error");

    if (!error) return;

    const destino = document.querySelector(
      "[data-login-error], [data-register-error]"
    );

    if (!destino) return;

    const mensajes = {
      datos: "Completá los campos correctamente. La contraseña debe tener al menos 8 caracteres.",
      existe: "Ese nombre de usuario o email ya está registrado.",
      credenciales: "El usuario/email o la contraseña no son correctos.",
      servidor: "Hubo un problema con el servidor. Intentá nuevamente."
    };

    destino.textContent =
      mensajes[error] || "No se pudo completar la operación.";
  }

  async function cargarComentarios() {
    const lista = document.querySelector("[data-comment-list]");

    if (!lista) return;

    try {
      const respuesta = await fetch("../php/listar_comentarios.php");
      const datos = await respuesta.json();

      lista.replaceChildren();

      if (!respuesta.ok) {
        throw new Error(datos.error || "No se pudieron cargar los comentarios.");
      }

      if (!datos.length) {
        const vacio = document.createElement("li");
        vacio.className = "comment";
        vacio.textContent =
          "Todavía no hay comentarios. ¡Podés iniciar la conversación!";

        lista.append(vacio);
        return;
      }

      datos.forEach((comentario) => {
        const item = document.createElement("li");
        item.className = "comment";

        const meta = document.createElement("div");
        meta.className = "comment__meta";

        const autor = document.createElement("span");
        autor.textContent =
          `${comentario.nombre_usuario} · ${comentario.categoria}`;

        const fecha = document.createElement("span");
        fecha.textContent = new Date(
          comentario.fecha_creacion.replace(" ", "T")
        ).toLocaleString("es-AR");

        const cuerpo = document.createElement("p");
        cuerpo.textContent = comentario.cuerpo;

        meta.append(autor, fecha);
        item.append(meta, cuerpo);
        lista.append(item);
      });
    } catch (error) {
      lista.textContent = error.message;
    }
  }

  const formulario = document.querySelector("[data-community-form]");

  if (formulario) {
    formulario.addEventListener("submit", async (evento) => {
      evento.preventDefault();

      const aviso = document.querySelector("[data-comment-error]");
      const boton = formulario.querySelector('button[type="submit"]');

      aviso.textContent = "";
      boton.disabled = true;

      try {
        const respuesta = await fetch("../php/publicar_comentario.php", {
          method: "POST",
          body: new FormData(formulario),
          credentials: "same-origin"
        });

        const datos = await respuesta.json();

        if (!respuesta.ok) {
          throw new Error(datos.error || "No se pudo publicar.");
        }

        formulario.querySelector("textarea").value = "";

        await cargarComentarios();

        aviso.textContent = "¡Comentario publicado!";
      } catch (error) {
        aviso.textContent = error.message;
      } finally {
        boton.disabled = false;
      }
    });
  }

  cargarSesion().then((usuario) => {
    if (usuario.logueado) {
      cargarComentarios();
    }
  });

  mostrarAvisoFormulario();
});