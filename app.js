'use strict';

var socket = new WebSocket('ws://localhost:3000');

socket.onopen = function() {
    console.log('Connection established');
}

var enviar = document.getElementById('btnEnviar');
enviar.addEventListener('click', sendMessage);

socket.onmessage = function(msg) {
    console.log(msg);
    // Ao receber uma mensagem
    document.getElementById('conversa').value += '## ' + msg.data + "\n";
};

socket.onclose = function() { 
    console.log('Conntection is closed.'); 
};

socket.onerror = function(err) { 
    console.log(err.data); 
};

function sendMessage() {
    var mensagem = document.getElementById('mensagem');

    if (mensagem.value !== '') {
        // Exibe a mensagem no chat
        document.getElementById('conversa').value += '>> ' + mensagem.value + "\n";

        // Envia a mensagem para o servidor
        socket.send(mensagem.value);

        // Limpa o campo de mensagem
        mensagem.value = '';
    }
}