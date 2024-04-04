const nameSpan = document.getElementById('alias');
const msgBlock = document.getElementById('msg-block');
const msgText = document.getElementById('msg-text');
msgText.addEventListener('input', (e) => {
    if (e.target.value !== "") {
        msgBtn.removeAttribute('disabled');
    } else {
        msgBtn.setAttribute('disabled', 'true');
    }
});

const msgBtn = document.getElementById('send');
username = "UUID";

ws = new WebSocket("wss://node8.webte.fei.stuba.sk:8443/wss");

ws.onopen = function(e) {
    
};

ws.onmessage = function(e) {
    // console.log(e.data)
    data = JSON.parse(e.data);
    if (data.uuid) {
        nameSpan.innerHTML = `@${data.uuid}`;
        username = data.uuid;
    }
    if (data.type === "message") {
        // console.log(data.payload);
        newMsg = document.createElement('article');
        newMsg.classList.add('pico-background-grey-100');
        
        if (data.sender === username) {
            newMsg.innerHTML = `<span class="pico-color-cyan-500">@${data.sender}:</span> ${data.payload}`;
        } else {
            newMsg.innerHTML = `<span class="pico-color-red-500">@${data.sender}:</span> ${data.payload}`;

        }

        msgBlock.appendChild(newMsg);
    }
};



const sendMessage = () => {
    if (msgText.value !== "") {
        message = {
            type: "message",
            payload: msgText.value,
            sender: username
        }
        ws.send(JSON.stringify(message));

        msgText.value = "";
        msgBtn.setAttribute('disabled', 'true');
    }

    // if (msgText.value !== "") {
        // newMsg = document.createElement('article');
        // newMsg.classList.add('pico-background-grey-100');
        // newMsg.innerHTML = `<span class="pico-color-cyan-500">@${username}:</span> ${msgText.value}`;

        // msgBlock.appendChild(newMsg);

        
    // }
}

