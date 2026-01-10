// Referencias a elementos del DOM
const modal = document.getElementById('chatModal');
const messages = document.getElementById('messages');
const input = document.getElementById('questionInput');
// const openBtn = document.getElementById('openChat');
const openBtn = document.getElementById('iaempatica');
const closeBtn = document.getElementById('closeChat');
const sendBtn = document.getElementById('sendBtn');
const clearBtn = document.getElementById('clearBtn');

// Abrir / cerrar diálogo soy carla
openBtn.addEventListener('click', () => {
  chat = document.getElementById('chatModal')
  chat.classList.toggle('visible');
  // chat.remove('hidden');
  //  modal.classList.remove('hidden');
});

closeBtn.addEventListener('click', () => {
  modal.classList.remove('visible');
});

clearBtn.addEventListener('click', () => {
  messages.innerHTML = '';
});

// Enviar con botón o ENTER
sendBtn.addEventListener('click', sendQuestion);

input.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    sendQuestion();
  }
});

// Añadir mensajes al chat
function addMessage(text, type) {
  const div = document.createElement('div');
  div.className = type;
  div.textContent = text;
  messages.appendChild(div);
  messages.scrollTop = messages.scrollHeight;
}

// Enviar pregunta al servicio mock
async function sendQuestion() {
  const question = input.value.trim();
  if (!question) return;

  addMessage(question, 'user');

  try {
    const url = `https://jsonplaceholder.typicode.com/posts/1`;


    // const url = `https://mi-servicio-notebooklm.mock/chat?question=${
    //     encodeURIComponent(question)}`;

    const response = await fetch(url);

    if (!response.ok) {
      throw new Error('Respuesta inválida del servidor');
    }



    // const response =
    //     await
    //     fetch('http://serv.nappgui.com/duser.php?user=amanda&pass=1234', {
    //       method: 'POST',
    //       headers: {'Content-Type': 'application/json'},
    //       body: JSON.stringify({question})
    //     });

    // if (!response.ok) {
    //   throw new Error('Respuesta inválida del servidor');
    // }

    const data = await response.json();
    addMessage(data.body, 'bot');
    input.value = '';

  } catch (error) {
    addMessage('No se pudo contactar con el servicio.', 'bot');
    console.error(error);
  }
}
